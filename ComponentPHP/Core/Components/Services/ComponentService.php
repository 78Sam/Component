<?php

declare(strict_types=1);

namespace Core\Components\Services;

use Core\Components\Exceptions\ComponentOverlapException;
use Core\Components\Models\Components\ComponentInterface;
use Core\Components\Models\Components\UnparsedComponent;
use Core\Utility\Services\PathService;

class ComponentService
{
    public const string VARIABLE_PATTERN = '/!@\(\s*\$(?<variable_name>[a-zA-Z_]{1}\w*)\s*\)/';
    public const string COMPONENT_PATTERN = '/!@\(\s*component\|(?<component_name>\w+)\s*\)(?<body>.*?)!@\(\s*end\s*\)/s';

    /**
     * @template T of ComponentInterface
     *
     * @param class-string<T> $model
     *
     * @return T
     */
    public function get(string $componentName, string $model, string $path, bool $absolutePath = false)
    {
        $components = $this->loadFile($path, $absolutePath);
        if (!array_key_exists($componentName, $components)) {
            return null;
        }

        return $this->parseComponent($components[$componentName], $model);
    }

    /**
     * @return array<string, UnparsedComponent>
     */
    public function loadFile(string $path, bool $absolutePath = false): array
    {
        $path = $absolutePath ? PathService::normalisePath($path) : PathService::fromProjectDirectory($path);

        $content = file_get_contents($path);
        if ($content === false) {
            throw new \Exception("Unable to read file '{$path}'");
        }

        $componentMatches = [];
        $matchResult = preg_match_all(self::COMPONENT_PATTERN, $content, $componentMatches, flags: PREG_SET_ORDER);
        if ($matchResult === 0 || $matchResult === false) {
            return [];
        }
        /** @var list<array{component_name: string, body: string}> $componentMatches */ // Intentionally below

        /** @var array<string, UnparsedComponent> $components */
        $components = [];
        foreach ($componentMatches as $componentMatch) {
            $componentName = $componentMatch['component_name'];
            if (array_key_exists($componentName, $components)) {
                throw new ComponentOverlapException($componentName, $path);
            }
            $components[$componentName] = new UnparsedComponent(
                $componentMatch['component_name'],
                $componentMatch['body'],
            );
        }

        return $components;
    }

    /**
     * @template T of ComponentInterface
     *
     * @param class-string<T> $model
     *
     * @return T
     */
    public function parseComponent(UnparsedComponent $unparsedComponent, string $model): ComponentInterface
    {
        /** @var array<string, string> $sockets */
        $sockets = [];

        /** @var array<string, list<string>> $variableMap */
        $variableMap = [];

        $componentBody = $unparsedComponent->body;
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
        $sockets['_socket_block_-1'] = $componentBody;

        $component = new $model($unparsedComponent->name, $sockets, $variableMap);
        if (!$component instanceof ComponentInterface) {
            throw new \Exception('Component not subclass of ComponentInterface'); // TODO: Improve
        }

        return $component;
    }
}
