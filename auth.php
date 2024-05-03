<?php



$EXPIRY = 60*1;


function startSession(string $role) {
    global $EXPIRY;

    session_start();
    if (!isset($_SESSION["uid"])) {
        $uid = bin2hex(random_bytes(128));
        $_SESSION["uid"] = $uid;
        setcookie(
            name: "uid",
            value: $uid,
            expires_or_options: time()+$EXPIRY,
            path: "/Test/",
            httponly: true,
            secure: true
        );
    }
}


function endSession() {
    session_start();
    session_unset();
    session_destroy();
    if (isset($_COOKIE["uid"])) {
        setcookie(name: "uid", value: "", expires_or_options: time() - 1);
    }
}









?>