<?php

declare(strict_types=1);

namespace App\Services;

final class DateTimeService
{
    public const string DATETIME_FORMAT = 'Y-m-d H:i:s';

    public static function fromString(string $datetime): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $datetime);
    }

    public static function toString(\DateTimeImmutable $datetime): string
    {
        return $datetime->format(self::DATETIME_FORMAT);
    }

    public static function stringNow(): string
    {
        return self::toString(new \DateTimeImmutable('now'));
    }
}
