<?php

function genny(array $tokens) {
    foreach ($tokens as $token) {
        yield $token;
    }
}

$gene = genny(['a', 'b', 'c']);
echo $gene->current() . PHP_EOL;
$gene->next();
echo $gene->current() . PHP_EOL;
$gene->next();
echo $gene->current() . PHP_EOL;
$gene->next();
echo ($gene->current() === null) . PHP_EOL;
$gene->next();
$gene->valid()