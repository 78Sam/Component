<?php


require_once(__DIR__ . "/middleware.php");


class Logout implements Middleware {

    public function __construct() {}

    public function apply() {
        session_start();
        session_unset();
        session_destroy();
    }

}


?>