<?php

declare(strict_types=1);

$x = [
    "a" => 10,
    "b" => 20,
];

array_filter($x, function($a) {
    print_r($a . PHP_EOL);
    // print_r($b . PHP_EOL);
});