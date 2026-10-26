<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Templates\SQL\UserTemplate;
use Core\Components\Models\Component;
use Core\Components\Models\Components\ComponentInterface;
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
        return $this->runGetUserQuery($this->userTemplate->getUserByIdQuery($id));
    }

    public function getUserByUsername(string $username): ?User
    {        
        return $this->runGetUserQuery($this->userTemplate->getUserByUsernameQuery($username));
    }

    private function runGetUserQuery(ComponentInterface $getUserComponent): ?User
    {
        $statement = $this->databaseService->query($getUserComponent);
        
        return $this->databaseService->getOneOrNullResult($statement, User::class, static function(array $row) {
            $row['joined'] = DateTimeService::fromString($row['joined']);

            return $row;
        });
    }
}
