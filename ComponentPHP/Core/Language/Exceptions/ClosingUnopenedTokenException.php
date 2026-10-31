<?php

declare(strict_types=1);

namespace Core\Language\Exceptions;

use Core\Language\Tokens\AbstractToken;

class ClosingUnopenedTokenException extends \Exception
{
    public function __construct(AbstractToken $token)
    {
        $tokenClass = $token::class;

        parent::__construct("Applied closing token '{$tokenClass}' without an existing opening token being present");
    }
}
