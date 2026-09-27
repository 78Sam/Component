<?php

declare(strict_types=1);

namespace Core\Sessions\Services;

class SessionService
{
    public static function startSession(): void
    {
        session_start();
        session_regenerate_id(true);

        $timestamp = (new \DateTimeImmutable('now'))->getTimestamp();

        $_SESSION["sam - {$timestamp}"] = "hi - {$timestamp}";
        dump($_SESSION);
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
}
