<?php


require_once(__DIR__ . "/middleware.php");


class Auth implements Middleware {

    private int $expiry;
    private Middleware $uses;

    public function __construct(int $expiry, Middleware $uses) {
        $this->expiry = $expiry;
        $this->uses = $uses;
    }

    public function log(string $message): void {
        error_log(
            message: "middleware/authenticate.php: '{$message}'"
        );
        return;
    }

    public function apply(): void {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (($resp = $this->control()) !== "") {
            session_unset();
            session_destroy();
            $this->log($resp);
            $this->uses->apply();
        }

        $this->log("Authentication successful");
        return;
    }

    private function control(): string {
    
        // Variables Set
    
        if (
            empty($_SESSION["login_state"]) ||
            empty($_SESSION["login_state"]["type"]) ||
            empty($_SESSION["login_state"]["email"]) ||
            empty($_SESSION["login_state"]["timestamp"])
        ) {
            return "Unset session variables";
        }
    
        // Correct Types
    
        if (
            !is_array($_SESSION["login_state"]) ||
            !is_string($_SESSION["login_state"]["type"]) ||
            !is_string($_SESSION["login_state"]["email"]) ||
            !is_int($_SESSION["login_state"]["timestamp"])
        ) {
            return "Session variables wrong type";
        }
    
        // Meaningful Values Set
    
        if ($_SESSION["login_state"]["timestamp"] > time() + 1) {
            return "Timestamp inconsistent";
        }
    
        // Expired Session
    
        if ($this->expiry) {
    
            if ($_SESSION["login_state"]["timestamp"] < (time() - $this->expiry)) {
                return "Login has expired";
            }
    
        }
    
        return "";

    }

}


?>