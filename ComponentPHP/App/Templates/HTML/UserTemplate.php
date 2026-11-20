<?php

declare(strict_types=1);

namespace App\Templates\HTML;

class UserTemplate extends AbstractBaseTemplate
{
    public const string COMPONENT_HTML_USERS_USER_CARD = 'App/Components/HTML/Users/user_card.html';

    #[\Override]
    public function getFiles(): array
    {
        return array_merge(
            parent::getFiles(),
            [
                self::COMPONENT_HTML_USERS_USER_CARD => self::COMPONENT_HTML_USERS_USER_CARD,
            ],
        );
    }
}
