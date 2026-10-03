<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Templates\SQL\UserTemplate;
use Core\Database\Services\DatabaseService;

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
        $statement = $this->databaseService->query($getUserByIdComponent);
        
        return $this->databaseService->getOneOrNullResult($statement, User::class, static function(array $row) {
            $row['joined'] = DateTimeService::fromString($row['joined']);

            return $row;
        });
    }
}
