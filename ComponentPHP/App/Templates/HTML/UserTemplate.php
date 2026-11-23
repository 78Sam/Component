<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Components\HTML\Users\UserCardSchematic;
use App\Models\User;
use Core\Components\Models\Components\ComponentInterface;

class UserTemplate extends AbstractBaseTemplate
{
    public const string COMPONENT_HTML_USERS_USER_CARD = 'App/Components/HTML/Users/user_card.html';

    #[\Override]
    protected function setup(): void
    {
        parent::setup();

        $this->loadFile(UserCardSchematic::getPath());
    }

    /**
     * @param list<User> $users
     */
    public function loadUserCards(array $users): ComponentInterface
    {
        $userComponents = [];
        foreach ($users as $user) {
            $userComponents[] = $this
                ->get(UserCardSchematic::COMPONENT_USER_CARD, UserCardSchematic::getPath())
                ->fillModel($user)
            ;
        }

        return $this->collect($userComponents);
    }
}
