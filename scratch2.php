<?php

abstract class X
{
    public string $hello;
}

class Y extends X {};

class Z extends X {};

$p = new Y();
$q = new Z();

$p::$hello = 'yo';
$q::$hello = 'yo2';

echo $p::$hello;
