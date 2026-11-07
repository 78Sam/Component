<?php

class Hello
{
    public function __construct(public readonly string $name)
    {
    }
}

$x = new Hello('x');
$y = new Hello('y');
$z = new Hello('z');

$a = [$x, $y];
$b = [$z];

$a = array_merge($a, $b);

var_dump($a);