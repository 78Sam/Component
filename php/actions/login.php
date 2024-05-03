<?php


$username = $_POST["username"];
$password = $_POST["password"];


if ($username == "sam" && $password == "sam") {
    setcookie(
        name: "admin",
        value: "admin", 
        expires_or_options: time() + 60,
        path: "/Test/",
        secure: true,
        httponly: true
    );
    echo "cookie set";
} else {
    header("Location: https://sam-mccormack.co.uk/Test/");
}



?>