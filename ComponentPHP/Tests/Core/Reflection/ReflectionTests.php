<?php

declare(strict_types=1);

namespace Tests\Core\Reflection;

use Core\DependencyInjection\Services\ReflectionService;
use Core\Tests\AbstractTest;
use Core\Tests\Attributes\Test;
use Tests\Core\Reflection\Include\TestClass;

class ReflectionTests extends AbstractTest
{
    #[Test('Simple test :)')]
    public function test(): void
    {
        $class = new \ReflectionClass(TestClass::class);
        $method = $class->getMethod('testMe');
        foreach ($method->getParameters() as $parameter) {
            print_r(ReflectionService::getType($parameter) . PHP_EOL);
        }
    }
}
