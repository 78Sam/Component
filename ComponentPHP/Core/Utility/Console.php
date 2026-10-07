<?php

declare(strict_types=1);

namespace Core\Utility;

// TODO: Move to services

final class Console
{
    public const string RESET = "\e[0m";

    public const string COLOUR_BLACK = '30';
    public const string COLOUR_RED = '31';
    public const string COLOUR_GREEN = '32';
    public const string COLOUR_YELLOW = '33';
    public const string COLOUR_BLUE = '34';
    public const string COLOUR_PURPLE = '35';
    public const string COLOUR_CYAN = '36';
    public const string COLOUR_WHITE = '37';

    public const string COLOUR_INTENSE_BLACK = '90';
    public const string COLOUR_INTENSE_RED = '91';
    public const string COLOUR_INTENSE_GREEN = '92';
    public const string COLOUR_INTENSE_YELLOW = '93';
    public const string COLOUR_INTENSE_BLUE = '94';
    public const string COLOUR_INTENSE_PURPLE = '95';
    public const string COLOUR_INTENSE_CYAN = '96';
    public const string COLOUR_INTENSE_WHITE = '97';

    public const string BG_COLOUR_BLACK = '40';
    public const string BG_COLOUR_RED = '41';
    public const string BG_COLOUR_GREEN = '42';
    public const string BG_COLOUR_YELLOW = '43';
    public const string BG_COLOUR_BLUE = '44';
    public const string BG_COLOUR_PURPLE = '45';
    public const string BG_COLOUR_CYAN = '46';
    public const string BG_COLOUR_WHITE = '47';

    public const string BG_COLOUR_INTENSE_BLACK = '100';
    public const string BG_COLOUR_INTENSE_RED = '101';
    public const string BG_COLOUR_INTENSE_GREEN = '102';
    public const string BG_COLOUR_INTENSE_YELLOW = '103';
    public const string BG_COLOUR_INTENSE_BLUE = '104';
    public const string BG_COLOUR_INTENSE_PURPLE = '105';
    public const string BG_COLOUR_INTENSE_CYAN = '106';
    public const string BG_COLOUR_INTENSE_WHITE = '107';

    public const string EM_REGULAR = '0';
    public const string EM_BOLD = '1';
    public const string EM_UNDERLINE = '4';

    public static function message(
        string $message,
        string $foreground = '',
        string $background = '',
        string $em = '',
        bool $newline = true,
    ): string {
        $codes = [$em, $foreground, $background];
        $codes = array_filter($codes, fn(string $code): bool => $code !== '');
        $code = implode(';', $codes);

        $message = "\e[{$code}m{$message}" . self::RESET;

        return $newline ? "{$message}\n" : $message;
    }
}
