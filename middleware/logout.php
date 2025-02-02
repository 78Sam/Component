<?php


require_once(__DIR__ . "/middleware.php");


class Logout implements Middleware {

    public function __construct() {}

    public function log(string $message): void {
        error_log(
            message: "middleware/logout.php: '{$message}'"
        );
        return;
    }

    public function apply(): void {
        session_start();
        session_unset();
        session_destroy();
        $this->log("Logged user out");
        return;
    }

}


?>