<?php

require_once(__DIR__ . "/../../dir.php");
require_once($REQUIRE_DATABASE);
require_once("{$MIDDLEWARE_FOLDER}authenticate.php");


abstract class Endpoint {

    protected Database $db;

    protected bool $requires_auth;
    protected array $auth_params;

    public function __construct(bool $requires_auth = true, array $auth_params = []) {

        $this->db = Database::getInstance();
        $this->requires_auth = $requires_auth;
        $this->auth_params = $auth_params;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($this->requires_auth) {
            $this->checkAuth();
        }

        return;

    }

    protected function checkAuth(): void {
        new Auth(
            SECONDS_UNTIL_LOGOUT,
            ...$this->auth_params
        )->apply();
        return;
    }

    protected abstract function checkpoint();

    protected abstract function log(string $message): void;

    /**
     * Location of redirect: header("Location: {$location}")
     * @param string $location
     * @return never
     */
    public function redirect(string $location): never {
        header("Location: {$location}");
        exit();
    }

}


?>