<?php

declare(strict_types=1);

namespace App\Config;

use App\Templates\SQL\UserTemplate;
use Core\Config\AbstractConfig;
use Core\Database\Services\DatabaseService;
use Core\Kernel;
use Core\Logging\Services\LoggingService;
use Core\Utility\Services\PathService;

final class DatabaseConfig extends AbstractConfig
{
    public function __construct(
        public readonly DatabaseService $databaseService,
        public readonly UserTemplate $userTemplate,
        private readonly LoggingService $loggingService,
    ) {
    }

    #[\Override]
    public function onKernelBoot(Kernel $kernel): void
    {
        $this->loggingService->log('Pre boot database config');

        $databasePath = PathService::fromProjectDirectory('App', 'Database', 'main.db');
        $databaseExists = file_exists($databasePath);

        $this->databaseService->connect("sqlite:{$databasePath}");
        if (!$databaseExists) {
            $schema = file_get_contents(PathService::fromProjectDirectory('App', 'Database', 'schema.sql'));
            $this->databaseService->connection->exec($schema);

            $createUserComponent = $this->userTemplate
                ->get('create_user')
                ->fillAll([
                    'username' => 'sam',
                    'password' => password_hash('sam', PASSWORD_DEFAULT),
                    'joined' => (new \DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                    'role' => '1',
                ])
            ;
            $this->databaseService->query($createUserComponent);
        }
    }
}
