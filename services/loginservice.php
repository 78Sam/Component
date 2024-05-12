<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"] . "/dir.php");
require_once($REQUIRE_DATABASE);
require_once($REQUIRE_SESSIONS);


function fallback(string $err=null) {
    global $URL_FALLBACK;
    // logout();
    endSession();
    if ($err) {
        header("Location: " . $URL_FALLBACK . "?err=" . $err);
    } else {
        header("Location: " . $URL_FALLBACK);
    }
    exit();
}


if ($URL_FALLBACK = $_SERVER["HTTP_REFERER"]) {
    if (str_contains($URL_FALLBACK, "?")) {
        $query_start = strpos($URL_FALLBACK, "?");
        $URL_FALLBACK = substr($URL_FALLBACK, 0, $query_start);
    }
} else {
    $URL_FALLBACK = $URL_HOME;
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

// if ($login_statement->fetch()) {
//     if (password_verify($password, $stored_password)) {
//         startSession("admin");
//         header("Location: " . $URL_HOME);
//         exit();
//     } else {
//         $login_statement->close();
//         fallback(err: "Invalid Password");
//     }
// } else {
//     $login_statement->close();
//     fallback(err: "Account doesn't exist");
// }

if ($stored_password) {
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