<?php

declare(strict_types=1);

namespace Core\Language\Tokens;

interface ClosingTokenInterface
{
    /**
     * @return list<class-string<AbstractToken>>
     */
    public static function closes(): array;
}
