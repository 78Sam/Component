<?php

declare(strict_types=1);

namespace Core\Testing;

use Core\Testing\Attributes\Test;

abstract class AbstractTest
{
    public function setup(): void {}

    public function teardown(): void {}

    public function preTest(Test $test): void {}

    public function postTest(Test $test): void {}

    /**
     * @throws \AssertionError
     */
    public static function assertEquals(mixed $value1, mixed $value2, string $message): void
    {
        if ($value1 !== $value2) {
            throw new \AssertionError($message);
        }
    }

    /**
     * @throws \AssertionError
     */
    public static function assertNotEquals(mixed $value1, mixed $value2, string $message): void
    {
        if ($value1 === $value2) {
            throw new \AssertionError($message);
        }
    }

    /**
     * @throws \AssertionError
     */
    public static function assertTrue(bool $result, string $message): void
    {
        if (!$result) {
            throw new \AssertionError($message);
        }
    }
}
