<?php

declare(strict_types=1);

namespace Tests\Core\Components;

use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;
use Tests\Core\Components\Include\Templates\TestTemplate;

class TemplateTests extends AbstractTest
{
    public TestTemplate $testTemplate;

    #[\Override]
    public function preTest(Test $test): void
    {
        $this->testTemplate = new TestTemplate();
    }

    #[Test('Load a simple component file via the template')]
    public function loadSimpleComponentFileTest()
    {
        $path = 'Tests/Core/Components/Include/Components/Simple.html';
        $this->testTemplate->loadFile($path);

        $firstComponentName = array_key_first($this->testTemplate->componentsByName);
        static::assertEquals(
            $firstComponentName,
            'simple',
            "Component 'simple' not loaded, but got '{$firstComponentName}' instead",
        );

        $firstFileName = array_key_first($this->testTemplate->componentsByFile);
        static::assertEquals($firstFileName, $path, "File '{$path}' not loaded, but got '{$firstFileName}' instead");

        // TODO: File -> Name test
        // TODO: instanceof Component test
    }
}
