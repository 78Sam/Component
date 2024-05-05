<?php

function startSession(string $role) {

    session_start();
    session_unset();
    session_destroy();
    session_regenerate_id();
    session_start();
    
    $uid = bin2hex(random_bytes(128));
    $_SESSION["login_state"] = [
        "uid"=>$uid,
        "role"=>$role,
        "timestamp"=>time()
    ];
}

function close(string $err=null) {
    global $FALLBACK;
    if ($err) {
        header("Location: " . $FALLBACK . "?err=" . $err);
    } else {
        header("Location: " . $FALLBACK);
    }
    exit();
}

if (!isset($_POST["email"], $_POST["password"])) {
    close(err: "Please enter email and password");
}

require_once("dir.php");
require_once("logout.php");
require_once($REQUIRE_DATABASE);

$FALLBACK = "https://sam-mccormack.co.uk/Test/loginpage";

$db = new Database($FALLBACK);
$link = $db->getConnection();

if (!$link) {
    close(err: "Failed to connect to database");
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
        header("Location: https://sam-mccormack.co.uk/Test/");
        exit();
    } else {
        $login_statement->close();
        close(err: "Invalid Password");
    }
} else {
    $login_statement->close();
    close(err: "Account doesn't exist");
}

?>