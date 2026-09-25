<?php

declare(strict_types=1);

namespace Core\Testing;

use Core\Testing\Attributes\Test;
use Core\Utility\ClassFinder;
use Core\Utility\Console;

final class TestRunner
{
    private ClassFinder $classFinder;
    /** @var list<\ReflectionClass<AbstractTest>> */
    private array $testClasses = [];

    public function __construct()
    {
        $this->classFinder = new ClassFinder();
        $this->testClasses = $this->classFinder->byExtension('Tests', AbstractTest::class);
    }

    public function runAllTests(): void
    {
        foreach ($this->testClasses as $testClass) {

            $tests = [];
            foreach ($testClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
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
            $this->runTests($testClass, $tests);
        }
    }

    /**
     * @param \ReflectionClass<AbstractTest> $class
     * @param list<array{test: Test, method: \ReflectionMethod}> $tests
     */
    public function runTests(\ReflectionClass $class, array $tests): void
    {
        print_r("Running tests for class {$class->name}\n");

        uasort($tests, function(array $testA, array $testB) {
            return $testB['test']->priority - $testA['test']->priority;
        });

        $class = new ($class->name)();
        $class->setup();

        foreach ($tests as $test) {
            $method = $test['method'];
            $testAttribute = $test['test'];

            $class->preTest($testAttribute);

            $testMessage = "{$method->name} [{$testAttribute->description}]";
            try
            {
                $method->invoke($class);
                print_r(' - ' . Console::message($testMessage, background: Console::BG_COLOUR_GREEN));
            } catch (\Throwable $th) {
                print_r(' - ' . Console::message("{$testMessage} ({$th->getMessage()})", background: Console::BG_COLOUR_RED));
            }

            $class->postTest($testAttribute);
        }

        $class->teardown();
        
        print_r("\n");
    }
}
