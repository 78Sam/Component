<?php

declare(strict_types=1);

namespace Tests\Core\Components;

use Core\Components\Services\ComponentService;
use Core\Testing\AbstractTest;
use Core\Testing\Attributes\Test;
use Tests\Core\Components\Include\Templates\TestTemplate;

class ComponentsTests extends AbstractTest
{
    private ComponentService $componentService;

    #[\Override]
    public function setup(): void
    {
        $this->componentService = new ComponentService();
    }

    #[Test('Load a simple component')]
    public function loadSimpleComponentTest()
    {
        $components = $this->componentService->loadFile('Tests/Core/Components/Include/Components/Simple.html');
        $componentNames = array_keys($components);

        static::assertEquals($componentNames, ['simple'], 'Should have loaded ["simple"]');
    }

    #[Test('Render a simple component')]
    public function renderSimpleComponentTest()
    {
        $renderedValue = $this->componentService
            ->get('simple', 'Tests/Core/Components/Include/Components/Simple.html')
            ?->render()
        ;
        $expectedRender = '<p>Simple</p>';

        static::assertEquals($renderedValue, $expectedRender, "Expected render '{$expectedRender}' got '{$renderedValue}'");
    }

    #[Test('Load complex components')]
    public function loadComplexComponentsTest()
    {
        $components = $this->componentService->loadFile('Tests/Core/Components/Include/Components/Variables.html');
        $componentNames = array_keys($components);

        static::assertEquals(
            $componentNames,
            ['complex_1', 'complex_2', 'complex_3'],
            'Should have loaded ["complex_1", "complex_2", "complex_3"],'
        );
    }

    #[Test('Render a single variable component')]
    public function renderSingleVariableComponentTest()
    {
        $renderedValue = $this->componentService
            ->get('complex_1', 'Tests/Core/Components/Include/Components/Variables.html')
            ?->fill('var', '1')
            ?->render()
        ;
        $expectedRender = '<p>Complex 1</p>';

        static::assertEquals($renderedValue, $expectedRender, "Expected render '{$expectedRender}' got '{$renderedValue}'");
    }

    #[Test('Render a duplicate variable')]
    public function renderDuplicateVariableTest()
    {
        $renderedValue = $this->componentService
            ->get('complex_2', 'Tests/Core/Components/Include/Components/Variables.html')
            ?->fill('var', '2')
            ?->render()
        ;
        $expectedRender = "<p>Complex 2_1</p>\n<p>Complex 2_2</p>";

        static::assertEquals($renderedValue, $expectedRender, "Expected render '{$expectedRender}' got '{$renderedValue}'");
    }

    #[Test('Render multiple variables')]
    public function renderMultipleVariables()
    {
        $renderedValue = $this->componentService
            ->get('complex_3', 'Tests/Core/Components/Include/Components/Variables.html')
            ?->fill('var1', 'var1')
            ?->fill('var2', 'var2')
            ?->render()
        ;
        $expectedRender = "<p>Complex var1_1</p>\n<p>Complex var1_2</p>\n<p>Complex var2</p>";

        static::assertEquals($renderedValue, $expectedRender, "Expected render '{$expectedRender}' got '{$renderedValue}'");
    }

    #[Test('Render a nested variables')]
    public function renderNestedComponent()
    {
        $innerComponent = $this->componentService
            ->get('use_me', 'Tests/Core/Components/Include/Components/Nested.html')
            ?->fill('value', '(nested value)')
        ;

        $renderedValue = $this->componentService
            ->get('nested', 'Tests/Core/Components/Include/Components/Nested.html')
            ?->fill('value', $innerComponent ?? 'fail')
            ?->render()
        ;

        $expectedRender = '<p>Nested value (nested value)</p>';

        static::assertEquals($renderedValue, $expectedRender, "Expected render '{$expectedRender}' got '{$renderedValue}'");
    }
}
