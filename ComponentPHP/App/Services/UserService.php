<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Templates\SQL\UserTemplate;
use Core\Components\Models\Component;
use Core\Database\Services\DatabaseService;
use Core\Utility\Services\DateTimeService;

final class UserService
{
    public function __construct(
        public readonly UserTemplate $userTemplate,
        public readonly DatabaseService $databaseService,
    ) {
    }

    public function getUserById(int $id): ?User
    {
        $getUserByIdComponent = $this->userTemplate
            ->get('get_user_by_id')
            ->fill('id', "{$id}")
        ;
        
        return $this->runGetUserQuery($getUserByIdComponent);
    }

    public function getUserByUsername(string $username): ?User
    {
        $getUserByUsernameComponent = $this->userTemplate
            ->get('get_user_by_username')
            ->fill('username', "{$username}")
        ;
        
        return $this->runGetUserQuery($getUserByUsernameComponent);
    }

    private function runGetUserQuery(Component $getUserComponent): ?User
    {
        $statement = $this->databaseService->query($getUserComponent);
        
        return $this->databaseService->getOneOrNullResult($statement, User::class, static function(array $row) {
            $row['joined'] = DateTimeService::fromString($row['joined']);

            return $row;
        });
    }
}
