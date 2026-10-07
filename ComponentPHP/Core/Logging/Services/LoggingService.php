<?php

declare(strict_types=1);

namespace Core\Logging\Services;

use Core\Debug\DebugMetrics;
use Core\Utility\Console;
use Core\Utility\Services\DateTimeService;

class LoggingService
{
    public const string LEVEL_INFO = 'info';
    public const string LEVEL_WARNING = 'warning';
    public const string LEVEL_ERROR = 'error';

    private const array LEVEL_MAP = [
        self::LEVEL_INFO => Console::BG_COLOUR_BLUE,
        self::LEVEL_WARNING => Console::BG_COLOUR_YELLOW,
        self::LEVEL_ERROR => Console::BG_COLOUR_RED,
    ];

    /** @var list<string> */
    private static array $logs = [];

    public function log(string $message, string $level = self::LEVEL_INFO): void
    {
        $background = self::LEVEL_MAP[$level] ?? self::LEVEL_MAP[self::LEVEL_INFO];

        $now = DateTimeService::stringNow(microseconds: true);
        $backtrace = DebugMetrics::getBacktrace(2);

        $log = "[{$now}] ({$backtrace})";
        static::$logs[] = "{$log} | {$message}";

        file_put_contents('php://stderr', Console::message($log, background: $background, newline: false) . " | {$message}\n");
    }

    public function writeLogs(string $path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), recursive: true);
        }
        file_put_contents($path, implode("\n", static::$logs) . "\n", flags: FILE_APPEND);
        static::$logs = [];
    }
}
