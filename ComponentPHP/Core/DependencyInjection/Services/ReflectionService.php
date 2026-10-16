<?php

declare(strict_types=1);

namespace Core\DependencyInjection\Services;

class ReflectionService
{
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

    /**
     * @return list<string>
     */
    public static function getTypes(\ReflectionParameter $parameter): array
    {
        $type = $parameter->getType();
        if ($type === null) {
            return [self::NATIVE_TYPE_MIXED];
        }

        if ($type instanceof \ReflectionUnionType) {
            $returnTypes = [];
            foreach ($type->getTypes() as $innerType) {
                $innerTypeName = $innerType->getName();
                if ($innerTypeName === self::NATIVE_TYPE_MIXED) {
                    return [self::NATIVE_TYPE_MIXED];
                }
                $returnTypes[] = $innerTypeName;
            }

            return $returnTypes;
        }

        return [$type->getName()];
    }

    public static function isNativeType(\ReflectionParameter $parameter): bool
    {
        foreach (self::getTypes($parameter) as $type) {
            if (!in_array($type, self::NATIVE_TYPES, true)) {
                return false;
            }
        }

        return true;
    }

    public static function doesTypeMatch(\ReflectionParameter $parameter, mixed $value): bool
    {
        $types = self::getTypes($parameter);
        if ($types === [self::NATIVE_TYPE_MIXED]) {
            return true;
        }

        foreach ($types as $type) {
            $isMatch = match($type) {
                self::NATIVE_TYPE_ARRAY => is_array($value),
                self::NATIVE_TYPE_BOOL => is_bool($value),
                self::NATIVE_TYPE_DOUBLE => is_double($value),
                self::NATIVE_TYPE_FLOAT => is_float($value),
                self::NATIVE_TYPE_INT => is_int($value),
                self::NATIVE_TYPE_NULL => is_null($value),
                self::NATIVE_TYPE_OBJECT => is_object($value),
                self::NATIVE_TYPE_STRING => is_string($value),
                default => $type === $value::class,
            };
            if ($isMatch) {
                return true;
            }
        }

        return false;
    }

    public static function castType(\ReflectionParameter $parameter, mixed $value): mixed
    {
        $types = self::getTypes($parameter);
        foreach ($types as $type) {
            if ($type === self::NATIVE_TYPE_MIXED) {
                return $value;
            }
        }

        if (count($types) > 1) {
            $typesString = implode(',', $types);

            throw new \LogicException("Cannot cast when there is more than one possible type: '{$typesString}'");
        }
        $type = array_first($types);

        if (!self::isNativeType($parameter) && !is_object($value)) {
            throw new \LogicException("Unable to cast to type '{$type}' if not already an object");
        }

        return match($type) {
            self::NATIVE_TYPE_ARRAY => (array) $value,
            self::NATIVE_TYPE_BOOL => (bool) $value,
            self::NATIVE_TYPE_DOUBLE, self::NATIVE_TYPE_FLOAT => (float) $value,
            self::NATIVE_TYPE_INT => (int) $value,
            self::NATIVE_TYPE_NULL => null,
            self::NATIVE_TYPE_OBJECT => (object) $value,
            self::NATIVE_TYPE_STRING => (string) $value,
            default => $value,
        };
    }
}
