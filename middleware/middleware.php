<?php


interface Middleware {

    public function apply();

    public function log(string $message);

}


?>