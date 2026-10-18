<?php

declare(strict_types=1);

namespace Tests\Core\Reflection;

use Core\DependencyInjection\Services\ReflectionService;
use Core\Routing\Attributes\Route;
use Core\Tests\AbstractTest;
use Core\Tests\Attributes\Test;
use Tests\Core\Reflection\Include\TestClass;

class ReflectionTests extends AbstractTest
{
    /** @var \ReflectionClass<TestClass> */
    private readonly \ReflectionClass $testClass;

    #[\Override]
    public function setup(): void
    {
        $this->testClass = new \ReflectionClass(TestClass::class);
    }

    // ! Single types

    #[Test('Single array param')]
    public function singleArrayParamTest(): void
    {
        $this->runSingleTypeTest('array', ReflectionService::NATIVE_TYPE_ARRAY);
    }

    #[Test('Single bool param')]
    public function singleBoolParamTest(): void
    {
        $this->runSingleTypeTest('bool', ReflectionService::NATIVE_TYPE_BOOL);
    }

    #[Test('Single float param')]
    public function singleFloatParamTest(): void
    {
        $this->runSingleTypeTest('float', ReflectionService::NATIVE_TYPE_FLOAT);
    }

    #[Test('Single int param')]
    public function singleIntParamTest(): void
    {
        $this->runSingleTypeTest('int', ReflectionService::NATIVE_TYPE_INT);
    }

    #[Test('Single mixed param')]
    public function singleMixedParamTest(): void
    {
        $this->runSingleTypeTest('mixed', ReflectionService::NATIVE_TYPE_MIXED);
    }

    #[Test('Single null param')]
    public function singleNullParamTest(): void
    {
        $this->runSingleTypeTest('null', ReflectionService::NATIVE_TYPE_NULL);
    }

    #[Test('Single object param')]
    public function singleObjectParamTest(): void
    {
        $this->runSingleTypeTest('object', ReflectionService::NATIVE_TYPE_OBJECT);
    }

    #[Test('Single string param')]
    public function singleStringParamTest(): void
    {
        $this->runSingleTypeTest('string', ReflectionService::NATIVE_TYPE_STRING);
    }

    #[Test('Single class param')]
    public function singleClassParamTest(): void
    {
        $method = $this->testClass->getMethod('classMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [TestClass::class], "Not a native class type, got: '{$typesString}'");
        static::assertFalse(ReflectionService::isNativeType($parameter), 'Single class found to be native');
    }

    // ! Union tests

    #[Test('Union int string class param')]
    public function unionIntStringClassParamTest(): void
    {
        $method = $this->testClass->getMethod('intStringUnionMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertArrayValuesEquals(
            $types,
            [ReflectionService::NATIVE_TYPE_INT, ReflectionService::NATIVE_TYPE_STRING],
            "Not a union of int and string, got: '{$typesString}'",
        );
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single class found to be native');
    }

    #[Test('Union int class null class param')]
    public function unionIntClassNullClassParamTest(): void
    {
        $method = $this->testClass->getMethod('intClassNullUnionMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertArrayValuesEquals(
            $types,
            [ReflectionService::NATIVE_TYPE_INT, TestClass::class, ReflectionService::NATIVE_TYPE_NULL],
            "Not a union of int TestClass and string, got: '{$typesString}'",
        );
        static::assertFalse(ReflectionService::isNativeType($parameter), 'Single class found to be native');
    }

    private function runSingleTypeTest(string $typeString, string $reflectionType): void
    {
        $method = $this->testClass->getMethod("{$typeString}Method");
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [$reflectionType], "Not a native string type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), "Single {$typeString} not found to be native");
    }
}
