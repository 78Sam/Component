<?php


class Block
{
    public function __construct(
        public readonly int $left,
        public readonly int $right,
        public readonly string $content,
    ) {
    }
}

abstract class AbstractTokenizer
{   
    private string $context = '';
    private int $leftPosition = 0;
    private int $rightPosition = 0;
    private int $contextLength = 0;

    private array $result = [];

    abstract protected function handle(string $char);

    public function tokenize(string $context): array
    {
        $this->context = $context;
        $this->leftPosition = 0;
        $this->rightPosition = 0;
        $this->contextLength = strlen($this->context);

        while (true) {

            $handler = $this->handle($this->peek(1)[0]);
            if ($handler) {
                $this->captureBlock('body');
                $handler();
            }

            if ($this->isEof()) {
                break;
            }

            $this->consume();
        }

        return $this->result;
    }

    protected function isEof(): bool
    {
        return $this->rightPosition >= $this->contextLength;
    }

    /**
     * @return list<?string>
     */
    protected function peek(int $steps): array
    {
        $chars = [];
        for ($index = 0; $index < $steps; $index++) {
            $chars[] = $this->context[$this->rightPosition + $index] ?? null;
        }

        return $chars;
    }

    protected function consume(): string
    {
        if ($this->isEof()) {
            throw new \Exception('Attempted to read beyond EOF');
        }

        return $this->context[$this->rightPosition++];
    }

    protected function captureBlock(string $block): self
    {
        $slice = substr($this->context, $this->leftPosition, $this->rightPosition - $this->leftPosition);
        // $this->result[] = new $block($this->leftPosition, $this->rightPosition, $slice);
        $this->result[] = [
            'left' => $this->leftPosition,
            'right' => $this->rightPosition,
            'block' => $block,
            'slice' => $slice,
        ];
        $this->leftPosition = $this->rightPosition;

        return $this;
    }
}

class ComponentTokenizer extends AbstractTokenizer
{
    #[\Override]
    protected function handle(string $char)
    {
        if ($char !== '!') {
            return null;
        }

        $buffer = $this->quickPeek(3);
        if ($buffer !== '!@(') {
            return;
        }

        $controlKeys = [
            '!@(component|' => fn() => $this->captureControlSequence('componentStart'),
            '!@(end' => fn() => $this->captureControlSequence('componentEnd'),
            '!@($' => fn() => $this->captureControlSequence('variable'),
        ];

        $longestKey = max(array_map(fn(string $key): int => strlen($key), array_keys($controlKeys)));

        for ($i = $longestKey; $i > 0; $i--) {
            $keyCheck = $this->quickPeek($i);
            // print_r("Looking at {$keyCheck}\n");
            if (!array_key_exists($keyCheck, $controlKeys)) {
                continue;
            }

            // print_r("Found {$keyCheck}\n");

            return $controlKeys[$keyCheck];
        }

        return null;
    }

    private function captureControlSequence(string $sequence): void
    {
        while ($this->consume() !== ')') {
            if ($this->isEof()) {
                throw new \Exception("Reached end of file while parsing {$sequence}");
            }
        }

        $this->captureBlock($sequence);
    }

    private function quickPeek(int $steps): string
    {
        return implode('', $this->peek($steps));
    }
}

$html = <<<'HTML'
    !@(component|test)
    <p>!@($variable)</p>
    !@(end)

    !@(component|test2)
    <p>!@($variable2)</p>
    !@(end)
    HTML;

$tkn = new ComponentTokenizer();
var_dump($tkn->tokenize($html));
