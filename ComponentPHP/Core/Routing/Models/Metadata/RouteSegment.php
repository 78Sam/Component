<?php

declare(strict_types=1);

namespace Core\Routing\Models\Metadata;

/**
 * I made this class lazy loaded to avoid having to do tonnes of regex pattern matching until its needed
 * Either smart or gonna bite me in the ass somehow
 */
final readonly class RouteSegment
{
    public const string SEGMENT_PATTERN = '/^(?<segment>[a-zA-Z0-9_-]+)$/';
    public const string VARIABLE_PATTERN = '/^{(?<variable>[a-zA-Z]+[0-9]*)+(:(?<pattern>.*))?}$/';
    public const string DEFAULT_SEGMENT_PATTERN = '[^/]+';

    public string $value;
    public ?string $variable;
    public bool $isDynamic;

    public function __construct(
        private string $segment,
    ) {
        $this->parseRouteSegment();
    }

    public function __toString()
    {
        return $this->value;
    }

    private function parseRouteSegment(): void
    {
        if (preg_match(self::SEGMENT_PATTERN, $this->segment) === 1) {
            $this->value = $this->segment;
            $this->isDynamic = false;

            return;
        }

        $matches = [];
        if (preg_match(self::VARIABLE_PATTERN, $this->segment, $matches) !== 1) {
            throw new \LogicException("Unable to parse segment: '{$this->segment}'");
        }

        $this->value = $matches['pattern'] ?? self::DEFAULT_SEGMENT_PATTERN;
        $this->variable = $matches['variable'];
        $this->isDynamic = true;
    }
}
