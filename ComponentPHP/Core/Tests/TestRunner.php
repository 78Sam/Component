<?php

declare(strict_types=1);

namespace Core\Tests;

use Core\Tests\Attributes\Test;
use Core\Utility\ClassFinder;
use Core\Utility\Console;

final class TestRunner
{
    public function __construct(
        public readonly ClassFinder $classFinder = new ClassFinder(),
    ) {}

    public function runAllTests(): void
    {
        /** @var list<\ReflectionClass<AbstractTest>> $testClasses */
        $testClasses = $this->classFinder->byExtension('Tests', AbstractTest::class);
        foreach ($testClasses as $testClass) {
            $this->runSpecificTestClass($testClass);
        }
    }

    public function runTestClass(string $class): void
    {
        $this->runSpecificTestClass(new \ReflectionClass($class));
    }

    /**
     * @param \ReflectionClass<AbstractTest> $class
     */
    private function runSpecificTestClass(\ReflectionClass $class): void
    {
        /** @var array{test: Test, method: \ReflectionMethod} */
        $tests = [];
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $testAttributes = $method->getAttributes(Test::class);
            if (count($testAttributes) === 0) {
                continue;
            }

            if (count($testAttributes) > 1) {
                throw new \LogicException('Each test method should only have one test attribute');
            }

            $testAttribute = $testAttributes[0]->newInstance();

            $tests[] = [
                'test' => $testAttribute,
                'method' => $method,
            ];
        }
        $this->runTests($class, $tests);
    }

    /**
     * @param \ReflectionClass<AbstractTest> $class
     * @param list<array{test: Test, method: \ReflectionMethod}> $tests
     */
    private function runTests(\ReflectionClass $class, array $tests): void
    {
        print_r("Running tests for class {$class->name}\n");

        uasort($tests, function (array $testA, array $testB) {
            if ($testA['test']->priority === 0) {
                return 1;
            }

            if ($testB['test']->priority === 0) {
                return -1;
            }

            return $testA['test']->priority - $testB['test']->priority;
        });

        $class = new $class->name();
        $class->setup();

        foreach ($tests as $test) {
            $method = $test['method'];
            $testAttribute = $test['test'];

            if (!$testAttribute->skipPreTest) {
                $class->preTest($testAttribute);
            }

            $testMessage = "{$method->name} [{$testAttribute->description}]";
            try {
                $method->invoke($class);
                print_r(' - ' . Console::message($testMessage, background: Console::BG_COLOUR_GREEN));
            } catch (\Throwable $th) {
                print_r(
                    ' - '
                        . Console::message("{$testMessage} ({$th->getMessage()})", background: Console::BG_COLOUR_RED),
                );
            }

            if (!$testAttribute->skipPostTest) {
                $class->postTest($testAttribute);
            }
        }

        $class->teardown();

        print_r("\n");
    }
}
