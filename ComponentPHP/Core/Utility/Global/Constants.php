<?php

declare(strict_types=1);

use Core\Utility\Services\PathService;

if (!defined('CPHP_ROOT_DIR')) {
    /** @var string */
    define('CPHP_ROOT_DIR', PathService::normalisePath(dirname(__DIR__, levels: 3)));
}
