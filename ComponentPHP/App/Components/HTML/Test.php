<?php

declare(strict_types=1);

namespace App\Components\HTML;

use Core\Utility\Paths\Models\Path;

class Test
{
    private static ?Path $path = null;

    public static function getPath(): Path
    {
        if (self::$path === null) {
            self::$path = new Path('App/Components/HTML/test.html');
            
            $path = self::$path->getPath();
            if (!file_exists($path)) {
                throw new \Exception("Failed to find component file: '{$path}'");
            }
        }

        return self::$path;
    }
}
