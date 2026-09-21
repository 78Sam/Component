<?php

declare(strict_types=1);

namespace Core\Components\Services;

use Core\Components\Models\Component;

class ComponentService
{
    public const string VARIABLE_PATTERN = '/!@\(\s*\$(?<variable_name>[a-zA-Z_]{1}\w*)\s*\)/';
    public const string COMPONENT_PATTERN = '/!@\(\s*component\|(?<component_name>\w+)\s*\)(?<body>.*?)!@\(\s*end\s*\)/s';

    public function get(string $componentName, string $path, bool $absolutePath = false): ?Component
    {
        return $this->loadFile($path, $absolutePath)[$componentName] ?? null;
    }

    /**
     * @return array<string, Component> The names of the components loaded via this file
     */
    public function loadFile(string $path, bool $absolutePath = false): array
    {
        if ($absolutePath === false) {
            $path = relativeToAbsolutePath($path);
        }
        $path = normalisePath($path);

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Unable to read file '{$path}'");
        }

        /** @var list<array{component_name: string}> $componentMatches */
        $componentMatches = [];
        $componentCount = preg_match_all(self::COMPONENT_PATTERN, $content, $componentMatches, flags: PREG_SET_ORDER);
        if ($componentCount === 0 || $componentCount === false) {
            return [];
        }

        $componentNames = [];
        foreach ($componentMatches as $componentMatch)
        {
            $componentName = $componentMatch['component_name'];
            if (array_key_exists($componentName, $componentNames))
            {
                throw new \Exception("Cannot load component as another component with the same name exists '{$componentName}'");
            }
            $componentNames[$componentName] = true;
        }

        return $this->parseComponents($componentMatches);
    }

    /**
     * @return array<string, Component>
     */
    private function parseComponents(array $componentMatches): array
    {
        /** @var array<string, Component> $components */
        $components = [];
        foreach ($componentMatches as $componentMatch) {

            /** @var array<string, string> $sockets */
            $sockets = [];

            /** @var array<string, list<string>> $variableMap */
            $variableMap = [];

            $componentBody = $componentMatch['body'];
            if ($componentBody[0] === "\n") {
                $componentBody = substr($componentBody, 1);
            }

            if ($componentBody[-1] === "\n") {
                $componentBody = substr($componentBody, 0, -1);
            }
            
            $variableMatches = [];
            preg_match_all(self::VARIABLE_PATTERN, $componentBody, $variableMatches, flags: PREG_SET_ORDER);
            foreach ($variableMatches as $index => $variable) {
                $split = explode($variable[0], $componentBody, limit: 2);

                $sockets["_socket_block_{$index}"] = $split[0];
                $variableName = $variable['variable_name'];
                $variablePseudonym = "_socket_variable_{$variableName}_{$index}";
                $sockets[$variablePseudonym] = '';

                if (!array_key_exists($variableName, $variableMap)) {
                    $variableMap[$variableName] = [];
                }
                $variableMap[$variableName][] = $variablePseudonym;

                $componentBody = $split[1];
            }
            $sockets["_socket_block_-1"] = $componentBody;

            $componentName = $componentMatch['component_name'];
            $components[$componentName] = new Component($componentName, $sockets, $variableMap);
        }

        return $components;
    }
}
