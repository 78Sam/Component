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
        return ($_SESSION['username'] ?? null) !== null;
    }

    public static function getUser(): ?User
    {
        $username = $_SESSION['username'] ?? null;
        if ($username !== null) {
            return null;
        }

        // return new User($username);
        return null;
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

        $_SESSION['username'] = $username;

        return new User($username, $joined, $role);
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

        $result = $this->databaseService->query($getUserComponent);
        if ($result === null) {
            return null;
        }

        $dbUser = $result->fetchAll(\PDO::FETCH_ASSOC);
        if (count($dbUser) !== 1) {
            return null;
        }

        if (!password_verify($password, $dbUser[0]['password'])) {
            return null;
        }

        $joined = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $dbUser[0]['joined']);
        $role = $dbUser[0]['role'];

        $_SESSION['username'] = $username;

        return new User($username, $joined, $role);
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
