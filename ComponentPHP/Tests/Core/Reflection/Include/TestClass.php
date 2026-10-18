<?php

declare(strict_types=1);

namespace Tests\Core\Reflection\Include;

class TestClass
{
    public function arrayMethod(array $x): void
    {}

    public function boolMethod(bool $x): void
    {}

    public function floatMethod(float $x): void
    {}

    public function intMethod(int $x): void
    {}

    public function mixedMethod(mixed $x): void
    {}

    public function nullMethod(null $x): void
    {}

    public function objectMethod(object $x): void
    {}

    public function stringMethod(string $x): void
    {}

    public function classMethod(TestClass $x): void
    {}

    public function intStringUnionMethod(int|string $x): void
    {}

    public function intClassNullUnionMethod(int|TestClass|null $x): void
    {}
}
