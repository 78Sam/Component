<?php

declare(strict_types=1);

namespace Core\Utility\Services;

final class DateTimeService
{
    public const string DATETIME_FORMAT = 'Y-m-d H:i:s';
    public const string DATETIME_FORMAT_MICROSECONDS = self::DATETIME_FORMAT . ':u';

    public static function fromString(string $datetime): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(self::DATETIME_FORMAT, $datetime);
    }

    public static function toString(\DateTimeImmutable $datetime, bool $microseconds = false): string
    {
        if ($microseconds) {
            return $datetime->format(self::DATETIME_FORMAT_MICROSECONDS);
        }

        return $datetime->format(self::DATETIME_FORMAT);
    }

    public static function stringNow(bool $microseconds = false): string
    {
        return self::toString(new \DateTimeImmutable('now'), $microseconds);
    }
}
