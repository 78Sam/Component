<?php


// $path = "";
// while (!file_exists($path . "dir.php")) {
//     $path = $path . "../";
// }
// require_once($path . "dir.php");
require_once("../dir.php");
require_once($REQUIRE_DATABASE);
require_once($REQUIRE_SESSIONS);


function fallback(string $err=null) {
    global $URL_FALLBACK;
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

if (!isset($_POST["email"], $_POST["password"]) || !$_POST["email"] || !$_POST["password"]) {
    fallback(err: "Please enter email and password");
}

$db = new Database();
if (!$db->connectionStatus()) {
    fallback(err: "Failed to connect to database");
}

$email = $_POST["email"];
$password = $_POST["password"];

$result = $db->query(
    query: "login",
    query_params: [["key"=>"email", "value"=>$email]]
);

if ($result === []) {
    fallback(err: "Account doesn't exist");
}

if (!password_verify($password, $result[0]["password_hash"])) {
    fallback(err: "Invalid Password");
}

startSession("admin");
header("Location: " . $URL_HOME);
exit();

?>