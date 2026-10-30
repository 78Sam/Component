<?php

declare(strict_types=1);

namespace Core\Language\Parsers;

abstract class AbstractParser implements ParserInterface
{
    abstract public function validate(array $tokens): void;
}
