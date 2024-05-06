<?php

require_once("dir.php");
require_once($REQUIRE_LOGOUT);
require_once($REQUIRE_DATABASE);


function startSession(string $role) {

    logout();
    session_regenerate_id();
    session_start();
    
    $uid = bin2hex(random_bytes(128));
    $_SESSION["login_state"] = [
        "uid"=>$uid,
        "role"=>$role,
        "timestamp"=>time()
    ];
}


function fallback(string $err=null) {
    global $URL_FALLBACK;
    logout();
    if ($err) {
        header("Location: " . $URL_FALLBACK . "?err=" . $err);
    } else {
        header("Location: " . $URL_FALLBACK);
    }
    exit();
}


if (!isset($_POST["email"], $_POST["password"])) {
    fallback(err: "Please enter email and password");
}

$db = new Database($URL_FALLBACK);
if (!$link = $db->getConnection()) {
    fallback(err: "Failed to connect to database");
}

$email = $_POST["email"];
$password = $_POST["password"];

$login_statement = $link->prepare("SELECT `password_hash` FROM `UserAccounts` WHERE `email`=?;");
$login_statement->bind_param("s", $email);
$login_statement->execute();
$login_statement->bind_result($stored_password);

if ($login_statement->fetch()) {
    if (password_verify($password, $stored_password)) {
        startSession("admin");
        header("Location: " . $URL_HOME);
        exit();
    } else {
        $login_statement->close();
        fallback(err: "Invalid Password");
    }
} else {
    $login_statement->close();
    fallback(err: "Account doesn't exist");
}

?>