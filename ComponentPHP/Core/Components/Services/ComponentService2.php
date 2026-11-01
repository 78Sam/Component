<?php

declare(strict_types=1);

namespace Core\Components\Services;

use Core\Components\Models\Sockets\VariableSocket;
use Core\Language\Parsers\Components\ComponentParser;
use Core\Language\Tokenizers\Components\ComponentTokenizer;
use Core\Language\Tokens\Components\ComponentStartToken;
use Core\Language\Tokens\Components\VariableToken;
use Core\Utility\Services\PathService;

class ComponentService2
{
    public function __construct(
        private readonly ComponentTokenizer $componentTokenizer,
        private readonly ComponentParser $componentParser,
    ) {
    }

    public function loadFile(string $path, bool $absolutePath = false): array
    {
        $path = $absolutePath ? PathService::normalisePath($path) : PathService::fromProjectDirectory($path);
        if (!file_exists($path)) {
            throw new \Exception("Cannot find file at: '{$path}'");
        }

        $tokens = $this->componentTokenizer->tokenize(file_get_contents($path));
        $this->componentParser->validate($tokens);

        // Note we have parsed the tokens so we don't have to validate correct syntax, e.g. open -> close

        $component = null;
        $components = [];
        $sockets = [];
        foreach ($tokens as $token) {
            if ($token instanceof ComponentStartToken) {
                $componentOpen = true;
            }

            if ($token instanceof VariableToken) {
                $sockets[] = new VariableSocket($token->getBody());
            }
        }
    }
}
