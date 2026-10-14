<?php

declare(strict_types=1);

namespace Core\DependencyInjection;

use Core\DependencyInjection\Exceptions\InfiniteRecursionException;

final class Container
{
    // TODO: Clear state between requests (maybe implements DI cache interface to keep warm?)
    // TODO: Instead of clearing everything, could we save like a schema of how to build a container for fast build each request after the first without leaking state?

    public const string NATIVE_TYPE_ARRAY = 'array';
    public const string NATIVE_TYPE_BOOL = 'bool';
    public const string NATIVE_TYPE_DOUBLE = 'double';
    public const string NATIVE_TYPE_FLOAT = 'float';
    public const string NATIVE_TYPE_INT = 'int';
    public const string NATIVE_TYPE_MIXED = 'mixed';
    public const string NATIVE_TYPE_NULL = 'null';
    public const string NATIVE_TYPE_OBJECT = 'object';
    public const string NATIVE_TYPE_STRING = 'string';

    public const array NATIVE_TYPES = [
        self::NATIVE_TYPE_ARRAY,
        self::NATIVE_TYPE_BOOL,
        self::NATIVE_TYPE_DOUBLE,
        self::NATIVE_TYPE_FLOAT,
        self::NATIVE_TYPE_INT,
        self::NATIVE_TYPE_MIXED,
        self::NATIVE_TYPE_NULL,
        self::NATIVE_TYPE_OBJECT,
        self::NATIVE_TYPE_STRING,
    ];

    public array $classes = [];

    private static ?Container $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function getType(\ReflectionParameter $parameter): string
    {
        return $parameter->getType()?->getName() ?? 'mixed';
    }

    public static function isNativeType(\ReflectionParameter $parameter): bool
    {
        return in_array(self::getType($parameter), self::NATIVE_TYPES, true);
    }

    public static function doesTypeMatch(\ReflectionParameter $parameter, mixed $value): bool
    {
        $type = self::getType($parameter);
        if ($type === self::NATIVE_TYPE_MIXED) {
            return true;
        }

        return match($type) {
            self::NATIVE_TYPE_ARRAY => is_array($value),
            self::NATIVE_TYPE_BOOL => is_bool($value),
            self::NATIVE_TYPE_DOUBLE => is_double($value),
            self::NATIVE_TYPE_FLOAT => is_float($value),
            self::NATIVE_TYPE_INT => is_int($value),
            self::NATIVE_TYPE_NULL => is_null($value),
            self::NATIVE_TYPE_OBJECT => is_object($value),
            self::NATIVE_TYPE_STRING => is_string($value),
            // default => throw new \LogicException("Unchecked type: '{$type}'")
            default => $type === $value::class,
        };
    }

    public static function castType(\ReflectionParameter $parameter, mixed $value): mixed
    {
        $type = self::getType($parameter);
        if ($type === self::NATIVE_TYPE_MIXED) {
            return $value;
        }

        return match($type) {
            self::NATIVE_TYPE_ARRAY => (array) $value,
            self::NATIVE_TYPE_BOOL => (bool) $value,
            self::NATIVE_TYPE_DOUBLE, self::NATIVE_TYPE_FLOAT => (float) $value,
            self::NATIVE_TYPE_INT => (int) $value,
            self::NATIVE_TYPE_NULL => null,
            self::NATIVE_TYPE_OBJECT => (object) $value,
            self::NATIVE_TYPE_STRING => (string) $value,
            // default => throw new \LogicException("Uncastable type: '{$type}'")
            default => $value,
        };
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
            if (!array_key_exists($parameterName, $parameters) && !self::isNativeType($parameter)) {
                $parameters[$parameterName] = $this->get(self::getType($parameter)); // getType shouldn't be null
            }
        }

        foreach ($parameters as $name => $value) {
            // If we have provided something that doesn't exist, remove it
            if (!array_key_exists($name, $methodParameters)) {
                unset($parameters[$name]);

                continue;
            }

            $methodParameter = $methodParameters[$name];
            if (!self::doesTypeMatch($methodParameter, $value)) {
                $parameters[$name] = self::castType($methodParameter, $value);
            }
        }

        return $parameters;
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
            if (self::isNativeType($parameter)) {
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
