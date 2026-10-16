<?php

declare(strict_types=1);

namespace Tests\Core\Reflection;

use Core\DependencyInjection\Services\ReflectionService;
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

    // ! Native single types

    #[Test('Single array param')]
    public function singleArrayParamTest(): void
    {
        $method = $this->testClass->getMethod('arrayMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_ARRAY], "Not a native array type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single array not found to be native');
    }

    #[Test('Single bool param')]
    public function singleBoolParamTest(): void
    {
        $method = $this->testClass->getMethod('boolMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_BOOL], "Not a native bool type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single bool not found to be native');
    }

    #[Test('Single float param')]
    public function singleFloatParamTest(): void
    {
        $method = $this->testClass->getMethod('floatMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_FLOAT], "Not a native float type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single float not found to be native');
    }

    #[Test('Single int param')]
    public function singleIntParamTest(): void
    {
        $method = $this->testClass->getMethod('intMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_INT], "Not a native int type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single int not found to be native');
    }

    #[Test('Single mixed param')]
    public function singleMixedParamTest(): void
    {
        $method = $this->testClass->getMethod('mixedMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_MIXED], "Not a native mixed type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single mixed not found to be native');
    }

    #[Test('Single null param')]
    public function singleNullParamTest(): void
    {
        $method = $this->testClass->getMethod('nullMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_NULL], "Not a native null type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single null not found to be native');
    }

    #[Test('Single object param')]
    public function singleObjectParamTest(): void
    {
        $method = $this->testClass->getMethod('objectMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_OBJECT], "Not a native object type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single object not found to be native');
    }

    #[Test('Single string param')]
    public function singleStringParamTest(): void
    {
        $method = $this->testClass->getMethod('stringMethod');
        $parameter = array_first($method->getParameters());

        $types = ReflectionService::getTypes($parameter);
        $typesString = implode(',', $types);

        static::assertEquals($types, [ReflectionService::NATIVE_TYPE_STRING], "Not a native string type, got: '{$typesString}'");
        static::assertTrue(ReflectionService::isNativeType($parameter), 'Single string not found to be native');
    }

    // ! Single custom class

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
}
