<?php

$x = [
    1,
    'hi',
    1.2,
    true,
    new \DateTime(),
    ['a'],
    null,
];

foreach ($x as $p) {
    echo gettype($p) . PHP_EOL;
}