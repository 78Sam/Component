<?php

declare(strict_types=1);

namespace Tests\Core\Reflection\Include;

class TestClass
{
    // public function intMethod(int $x): void
    // {}

    // public function stringMethod(string $x): void
    // {}

    // public function arrayMethod(array $x): void
    // {}

    // public function boolMethod(bool $x): void
    // {}

    // public function objectMethod(object $x): void
    // {}

    // public function floatMethod(float $x): void
    // {}

    public function testMe(int|string $hello): ?bool
    {
        return null;
    }
}
