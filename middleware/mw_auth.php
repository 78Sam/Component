<?php


require_once(__DIR__ . "/middleware.php");


class Auth implements Middleware {

    private int $expiry;
    private string $target;
    private string $fallback;
    private bool $starting;
    private Middleware $uses;

    public function __construct(int $expiry, string $target, Middleware $uses) {
        $this->expiry = $expiry;
        $this->target = $target;
        $this->uses = $uses;
    }

    public function apply() {
        if (!$this->control()) {
            setcookie(
                name: "target",
                value: $this->target,
                expires_or_options: time() + 60
            );
            $this->uses->apply();
        }
    }

    private function control() {

        session_start();
    
        // Variables Set
    
        if (
            !isset(
                $_SESSION["login_state"],
                $_SESSION["login_state"]["uid"],
                $_SESSION["login_state"]["role"]
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
            !is_string($_SESSION["login_state"]["uid"]) ||
            !is_string($_SESSION["login_state"]["role"]) ||
            !is_int($_SESSION["login_state"]["timestamp"])
        ) {
            // echo "2";
            session_unset();
            session_destroy();
            return false;
        }
    
        // Meaningful Values Set
    
        if (
            strlen($_SESSION["login_state"]["uid"]) !== 256 ||
            $_SESSION["login_state"]["role"] === "" ||
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