<?php

declare(strict_types=1);

namespace App\Templates\SQL;

use Core\Components\Models\Templates\AbstractTemplate;
use Core\Components\Models\Components\SQLComponent;

class UserTemplate extends AbstractTemplate
{
    public const string USERS_FILE = 'App/Components/SQL/users.sql';

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(self::USERS_FILE);
    }

    public function getCreateUserQuery(string $username, string $password, string $joined, string $role): SQLComponent
    {
        return $this
            ->get('create_user', SQLComponent::class, self::USERS_FILE)
            ->fillAll([
                'username' => $username,
                'password' => $password,
                'joined' => $joined,
                'role' => $role,
            ])
        ;
    }

    public function getLoginUserByUsername(string $username): SQLComponent
    {
        return $this
            ->get('get_login_user_by_username', SQLComponent::class, self::USERS_FILE)
            ->fill('username', $username)
        ;
    }

    public function getUserByIdQuery(int $id): SQLComponent
    {
        return $this
            ->get('get_user_by_id', SQLComponent::class, self::USERS_FILE)
            ->fill('id', (string) $id)
        ;
    }

    public function getUserByUsernameQuery(string $username): SQLComponent
    {
        return $this
            ->get('get_user_by_username', SQLComponent::class, self::USERS_FILE)
            ->fill('username', $username)
        ;
    }
}
