<?php

declare(strict_types=1);

$x = ['a', 'b', 'c', 'e', 'z', 'g'];
$y = ['b', 'a', 'z', 'g', 'e', 'c'];

$t1 = microtime(true);

sort($x);
sort($y);
if ($x === $y) {
    echo "Yeah 1\n";
}

$tres2 = microtime(true) - $t1;
echo $tres2 . PHP_EOL;

$t1 = microtime(true);

if (array_diff($x, $y) === []) {
    echo "Yeah 2\n";
}

$tres3 = microtime(true) - $t1;
echo $tres3 . PHP_EOL;

if ($tres2 < $tres3) {
    echo 'Yeah';
}