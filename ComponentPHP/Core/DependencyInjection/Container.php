<?php

declare(strict_types=1);

namespace Core\DependencyInjection;

use Core\DependencyInjection\Exceptions\InfiniteRecursionException;

class Container
{
    // TODO: Clear state between requests (maybe implements DI cache interface to keep warm?)
    // TODO: Instead of clearing everything, could we save like a schema of how to build a container for fast build each request after the first without leaking state?

    public const array NATIVE_TYPES = [
        'int',
        'bool',
        'string',
        'float',
        'array',
        'object',
        'mixed',
    ];

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

    private function buildDI(string $classname, array $walked = []): object
    {
        if (array_key_exists($classname, $walked)) {
            throw new InfiniteRecursionException($classname);
        }
        $walked[$classname] = true;

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
            if (in_array($type, self::NATIVE_TYPES, true)) {
                if (!$parameter->isOptional()) {
                    throw new \Exception("Cannot build DI due to required arguments of type '{$type}'");
                }
                continue;
            }

            if (!array_key_exists($type, $this->classes)) {
                $this->classes[$type] = $this->buildDI($type, $walked);
            }

            $vals[$parameter->getName()] = $this->classes[$type];
        }

        if ($staticConstructor) {
            return $constructor->invoke(null, ...$vals);
        }

        return $reflectionClass->newInstance(...$vals);
    }
}
