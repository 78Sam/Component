<?php

$x = ['ab', 'abc'];

var_dump(array_map(fn(string $item) => strlen($item), $x));
