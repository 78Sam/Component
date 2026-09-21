<?php

declare(strict_types=1);

namespace Tests\Core\Validators;

use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

class ValidatorTests extends AbstractTest
{
    #[Test('Test a required string')]
    public function stringTest(): void
    {
        $requirements = [
            'key' => new StringValidator('key'),
        ];
        ValidatorService::validate($requirements, [
            'key' => 'value',
        ]);
        static::assertEquals($requirements['key']->getValue(), 'value', 'Failed to validate a string');
    }

    #[Test('Test a required int')]
    public function intTest(): void
    {
        $requirements = [
            'key' => new IntOrStringIntValidator('key'),
        ];
        ValidatorService::validate($requirements, [
            'key' => 12,
        ]);
        static::assertEquals($requirements['key']->getValue(), 12, 'Failed to resolve an int');
    }

    #[Test('Test a string int')]
    public function stringIntTest(): void
    {
        $requirements = [
            'key' => new IntOrStringIntValidator('key'),
        ];
        ValidatorService::validate($requirements, [
            'key' => '-1',
        ]);
        static::assertEquals($requirements['key']->getValue(), -1, 'Failed to resolve a string int');
    }

    #[Test('Test nullable keys')]
    public function nullableKeyTest(): void
    {
        $requirements = [
            'key' => new IntOrStringIntValidator('key'),
        ];
        ValidatorService::validate($requirements, []);
        static::assertEquals($requirements['key']->getValueWithDefault(null), null, 'Failed to resolve a nullable key');
    }

    #[Test('Test a missing required value')]
    public function missingRequiredValueTest(): void
    {
        $requirements = [
            'key' => new StringValidator('key'),
        ];
        ValidatorService::validate($requirements, [
            'randomKey' => 'someValue',
        ]);
        static::assertTrue($requirements['key']->isMissing(), 'Failed to remove an undefined key');
    }
}
