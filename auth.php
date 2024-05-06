<?php

require_once("logoutservice.php");

function checkAuth(): bool {

    $EXPIRY = 60*5; // In seconds

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
        logout();
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
        logout();
        return false;
    }

    // Meaningful Values Set

    if (
        strlen($_SESSION["login_state"]["uid"]) !== 256 ||
        $_SESSION["login_state"]["role"] === "" ||
        $_SESSION["login_state"]["timestamp"] > time() + 1
    ) {
        // echo "3";
        logout();
        return false;
    }

    // Expired Session

    if ($EXPIRY) {

        if ($_SESSION["login_state"]["timestamp"] < (time() - $EXPIRY)) {
            // echo "4";
            logout();
            return false;
        }

    }

    return true;

}

?>