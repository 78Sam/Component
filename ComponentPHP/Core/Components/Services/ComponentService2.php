<?php

declare(strict_types=1);

namespace Core\Components\Services;

use Core\Components\Models\Components\Component;
use Core\Components\Models\Sockets\SocketMap;
use Core\Language\Nodes\BranchNodeInterface;
use Core\Language\Nodes\Components\ComponentNode;
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
    public function loadFile(string $path, bool $absolutePath = false)
    {
        $path = $absolutePath ? PathService::normalisePath($path) : PathService::fromProjectDirectory($path);
        if (!file_exists($path)) {
            throw new \Exception("Cannot find file at: '{$path}'");
        }

        $tokens = $this->componentTokenizer->tokenize(file_get_contents($path));
        $rootNode = $this->componentParser->parse($tokens);

        /** @var array<string, Component> $components */
        $components = [];
        $componentNodes = $rootNode->getChildren();
        foreach ($componentNodes as $componentNode) {
            if (!$componentNode instanceof ComponentNode) {
                continue;
            }

            $socketMap = new SocketMap();
            foreach ($this->flatten($componentNode) as $leafNode) {
                if ($leafNode instanceof VariableNode) {
                    $socketMap->add($leafNode->getValue(), '');

                    continue;
                }
                $socketMap->add('_block', $leafNode->getValue());
            }
            $components[$componentNode->name] = new Component($componentNode->name, $socketMap);
        }

        return $components;
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
