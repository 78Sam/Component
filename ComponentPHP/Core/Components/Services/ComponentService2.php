<?php

declare(strict_types=1);

namespace Core\Components\Services;

use Core\Language\Nodes\BranchNodeInterface;
use Core\Language\Nodes\Components\VariableNode;
use Core\Language\Nodes\LeafNodeInterface;
use Core\Language\Parsers\Components\ComponentParser;
use Core\Language\Tokenizers\Components\ComponentTokenizer;
use Core\Utility\Services\PathService;

class ComponentService2
{
    public function __construct(
        private readonly ComponentTokenizer $componentTokenizer,
        private readonly ComponentParser $componentParser,
    ) {}

    // TODO: Should we make all paths actually be an instance of PathService so we never have to double check
    public function loadFile(string $path, bool $absolutePath = false): array
    {
        $path = $absolutePath ? PathService::normalisePath($path) : PathService::fromProjectDirectory($path);
        if (!file_exists($path)) {
            throw new \Exception("Cannot find file at: '{$path}'");
        }

        $tokens = $this->componentTokenizer->tokenize(file_get_contents($path));
        $rootNode = $this->componentParser->parse($tokens);

        $sockets = [];
        $variableMap = [];
        $counter = -1;
        foreach ($this->flatten($rootNode) as $leafNode) {
            $counter++;

            if (!$leafNode instanceof VariableNode) {
                $sockets["_block_{$counter}"] = $leafNode->getValue();

                continue;
            }

            $variableName = $leafNode->getValue();
            if (!array_key_exists($variableName, $variableMap)) {
                $variableMap[$variableName] = [];
            }

            $key = "_variable_{$variableName}_{$counter}";
            $variableMap[$variableName][] = $key;
            $sockets[$key] = '';
        }

        var_dump($sockets);
        var_dump(implode('', $sockets));

        return $this->flatten($rootNode);
    }

    /**
     * @return list<LeafNodeInterface>
     */
    private function flatten(BranchNodeInterface $node): array
    {
        $nodes = [];
        foreach ($node->getChildren() as $childNode) {
            if ($childNode instanceof LeafNodeInterface) {
                $nodes[] = $childNode;

                continue;
            }
            $nodes = array_merge($nodes, $this->flatten($childNode));
        }

        return $nodes;
    }
}
