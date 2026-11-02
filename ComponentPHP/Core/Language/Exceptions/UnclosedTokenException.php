<?php

declare(strict_types=1);

namespace Core\Language\Exceptions;

use Core\Language\Tokens\AbstractToken;

class UnclosedTokenException extends \Exception
{
    public function __construct(AbstractToken $token)
    {
        $tokenClass = $token::class;

        parent::__construct("'{$tokenClass}' was never closed");
    }
}
