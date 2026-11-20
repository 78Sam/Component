<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Components\HTML\Users\UserCardSchematic;

class UserTemplate extends AbstractBaseTemplate
{
    public const string COMPONENT_HTML_USERS_USER_CARD = 'App/Components/HTML/Users/user_card.html';

    #[\Override]
    protected function setup(): void
    {
        parent::setup();

        $this->loadFile(UserCardSchematic::getPath());
    }
}
