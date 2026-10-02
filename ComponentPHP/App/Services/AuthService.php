<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Templates\SQL\UserTemplate;
use Core\Database\Services\DatabaseService;
use Core\Routing\Models\Request;
use Core\Utility\Validators\Exceptions\ValidationException;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\StringValidator;

class AuthService
{
    public function __construct(
        private readonly UserTemplate $userTemplate,
        private readonly DatabaseService $databaseService,
    ) {
    }

    public static function isAuthenticated(): bool
    {
        return static::getUser() !== null;
    }

    public static function getUser(): ?User
    {
        $user = $_SESSION['user'] ?? null;
        if ($user === null) {
            return null;
        }

        return unserialize($user);
    }

    public function register(Request $request): ?User
    {
        $registrationDetails = $this->parseUsernamePassword($request);
        if ($registrationDetails === null) {
            return null;
        }

        $username = $registrationDetails['username'];
        $password = $registrationDetails['password'];
        $joined = new \DateTimeImmutable('now');
        $role = 0;

        $getUserComponent = $this->userTemplate
            ->get('create_user')
            ->fillAll([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'joined' => $joined->format('Y-m-d H:i:s'),
                'role' => (string) $role,
            ])
        ;

        $result = $this->databaseService->query($getUserComponent);
        if ($result === null) {
            return null;
        }

        $user = new User($username, $joined, $role);
        $_SESSION['user'] = serialize($user);

        return $user;
    }

    public function login(Request $request): ?User
    {
        $loginDetails = $this->parseUsernamePassword($request);
        if ($loginDetails === null) {
            return null;
        }

        $username = $loginDetails['username'];
        $password = $loginDetails['password'];

        $getUserComponent = $this->userTemplate
            ->get('get_user_by_username')
            ->fill('username', $username)
        ;

        $statement = $this->databaseService->query($getUserComponent);
        if ($statement === null) {
            return null;
        }

        $dbUsers = $this->databaseService->getArrayResult($statement);
        if (count($dbUsers) !== 1) {
            return null;
        }
        $dbUser = $dbUsers[0];

        if (!password_verify($password, $dbUser['password'])) {
            return null;
        }

        $joined = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dbUser['joined']);
        $role = $dbUser['role'];

        $user = new User($username, $joined, $role);
        $_SESSION['user'] = serialize($user);

        return $user;
    }

    /**
     * @return ?array{username: string, password: string}
     */
    private function parseUsernamePassword(Request $request): ?array
    {
        $requirements = [
            'username' => new StringValidator('username'),
            'password' => new StringValidator('password'),
        ];

        ValidatorService::validate($requirements, $request->post);

        $username = $requirements['username']->getValueWithDefault();
        if ($username instanceof ValidationException || $username === null) {
            return null;
        }

        $password = $requirements['password']->getValueWithDefault();
        if ($password instanceof ValidationException || $password === null) {
            return null;
        }

        return ['username' => $username, 'password' => $password];
    }
}
