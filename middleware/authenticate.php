<?php


require_once(__DIR__ . "/middleware.php");


class Auth implements Middleware {

    private int $expiry;
    private Middleware $uses;

    public function __construct(int $expiry, Middleware $uses) {
        $this->expiry = $expiry;
        $this->uses = $uses;
    }

    public function apply(): void {
        if (!$this->control()) {
            $this->uses->apply();
        }
    }

    private function control(): bool {

        session_start();
    
        // Variables Set
    
        if (
            !isset(
                $_SESSION["login_state"],
                $_SESSION["login_state"]["type"],
                $_SESSION["login_state"]["email"]
            )
        ) {
            // echo "1";
            session_unset();
            session_destroy();
            return false;
        }
    
        // Correct Types
    
        if (
            !is_array($_SESSION["login_state"]) ||
            !is_string($_SESSION["login_state"]["type"]) ||
            !is_string($_SESSION["login_state"]["email"]) ||
            !is_int($_SESSION["login_state"]["timestamp"])
        ) {
            // echo "2";
            session_unset();
            session_destroy();
            return false;
        }
    
        // Meaningful Values Set
    
        if (
            $_SESSION["login_state"]["type"] === "" ||
            $_SESSION["login_state"]["email"] === "" ||
            $_SESSION["login_state"]["timestamp"] > time() + 1
        ) {
            // echo "3";
            session_unset();
            session_destroy();
            return false;
        }
    
        // Expired Session
    
        if ($this->expiry) {
    
            if ($_SESSION["login_state"]["timestamp"] < (time() - $this->expiry)) {
                // echo "4";
                session_unset();
                session_destroy();
                return false;
            }
    
        }
    
        return true;

    }

}


?>