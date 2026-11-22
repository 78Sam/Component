<?php

declare(strict_types=1);

namespace App\Components\HTML\Users;

use Core\Components\Models\Components\AbstractSchematic;

final class UserCardSchematic extends AbstractSchematic
{
    public const string COMPONENT_USER_CARD = 'user_card';

    #[\Override]
    protected static function getRawPath(): string
    {
        return 'App/Components/HTML/users/user_card_component.html';
    }
}
