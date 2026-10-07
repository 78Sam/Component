<?php

declare(strict_types=1);

namespace Core\Sessions\Services;

use Core\Logging\Services\LoggingService;

class SessionService
{
    public static function startSession(): void
    {
        session_start();
        session_regenerate_id(true);

        // $timestamp = (new \DateTimeImmutable('now'))->getTimestamp();
        // $_SESSION["sam - {$timestamp}"] = "hi - {$timestamp}";
        // dump($_SESSION);
    }

    public static function closeSession(): void
    {
        session_write_close();
        session_unset();
    }

    public static function destroySession(): void
    {
        session_unset();
        session_destroy();
        static::deleteSessionCookie();
    }

    public static function deleteSessionCookie(): void
    {
        setcookie('PHPSESSID', '', 0);
    }

    public static function sessionWrite(string $key, string $data): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \Exception('Cannot write to an inactive sesison');
        }

        $_SESSION[$key] = $data;
    }

    public static function sessionPop(string $key): ?string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \Exception('Cannot pop from an inactive sesison');
        }

        if (array_key_exists($key, $_SESSION)) {
            $data = $_SESSION[$key];
            unset($_SESSION[$key]);

            return $data;
        }

        return null;
    }
}
