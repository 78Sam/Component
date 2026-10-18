<?php

declare(strict_types=1);

namespace Core\DependencyInjection;

use Core\DependencyInjection\Exceptions\InfiniteRecursionException;
use Core\DependencyInjection\Services\ReflectionService;

final class Container
{
    // TODO: Clear state between requests (maybe implements DI cache interface to keep warm?)
    // TODO: Instead of clearing everything, could we save like a schema of how to build a container for fast build each request after the first without leaking state?

    public array $classes = [];

    private static ?Container $instance = null;

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Strip parameters that don't exist in the method, attempt to type cast, and container fetch objects
     *
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    public function buildMethodParameters(\ReflectionMethod $method, array $parameters): array
    {
        $methodParameters = [];
        foreach ($method->getParameters() as $parameter) {
            $parameterName = $parameter->name;
            $methodParameters[$parameterName] = $parameter;

            // If we don't have the parameter and its an object, try and create it with the container
            if (!array_key_exists($parameterName, $parameters) && !ReflectionService::isNativeType($parameter)) {
                $classString = array_first(ReflectionService::getTypes($parameter)); // TODO: This needs improved, if its a union what class do we take?
                $parameters[$parameterName] = $this->get($classString);
            }
        }

        foreach ($parameters as $name => $value) {
            // If we have provided something that doesn't exist, remove it
            if (!array_key_exists($name, $methodParameters)) {
                unset($parameters[$name]);

                continue;
            }

            $methodParameter = $methodParameters[$name];
            if (!ReflectionService::doesTypeMatch($methodParameter, $value)) {
                $parameters[$name] = ReflectionService::castType($methodParameter, $value);
            }
        }

        return $parameters;
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $classname
     *
     * @return T
     */
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
            if (ReflectionService::isNativeType($parameter)) {
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
