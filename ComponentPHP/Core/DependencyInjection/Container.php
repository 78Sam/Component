<?php

declare(strict_types=1);

namespace Core\DependencyInjection;

class Container
{
    public array $classes = [];

    private static ?Container $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function get(string $classname): object
    {
        if (array_key_exists($classname, $this->classes)) {
            return $this->classes[$classname];
        }

        $object = $this->buildDI($classname);
        $this->classes[$classname] = $object;

        return $object;
    }

    private function buildDI(string $classname): object
    {
        $reflectionClass = new \ReflectionClass($classname);
        $constructor = $reflectionClass->getConstructor();

        if ($constructor === null) {
            return $reflectionClass->newInstance();
        }

        $staticConstructor = false;
        if ($constructor->getModifiers() === \ReflectionMethod::IS_PRIVATE) {
            if (!$reflectionClass->hasMethod('getInstance')) {
                throw new \Exception("Class has private constructor and no 'getInstance' method");
            }

            $constructor = $reflectionClass->getMethod('getInstance');
            $staticConstructor = true;

            $modifiers = $constructor->getModifiers();
            if ($modifiers !== (\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC)) {
                throw new \Exception("'getInstance' method should be public static ({$modifiers})");
            }
        }

        $vals = [];
        foreach ($constructor->getParameters() as $parameter) {
            $type = $parameter->getType()->getName();
            if (in_array($type, ['int', 'bool', 'string', 'float', 'array', 'object', 'mixed'])) {
                if (!$parameter->isOptional()) {
                    throw new \Exception("Cannot build DI due to required arguments of type '{$type}'");
                }
                continue;
            }

            if (!array_key_exists($type, $this->classes)) {
                $this->classes[$type] = $this->buildDI($type);
            }

            $vals[$parameter->getName()] = $this->classes[$type];
        }

        if ($staticConstructor) {
            return $constructor->invoke(null, ...$vals);
        }

        return $reflectionClass->newInstance(...$vals);
    }
}
