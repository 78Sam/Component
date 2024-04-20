<?php

// Remove if not required, security risk

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base requirement 

require("dir.php");
require_once('vendor/autoload.php');

// Extra requirements

require($REQUIRE_COMPONENTS);

// DOTENV

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Test</title>
        <link rel="icon" type="image/x-icon" href="assets/pingu.png">

        <link rel="stylesheet" href="styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    </head>
    <body>
        
        <?php

            $comp = _component(
                name: "main",
                attributes: ["custom-style"=>"color: red; background-color: blue;"],
                values: ["main-heading"=>$_ENV["USER"], "subbyheading"=>"World", "para"=>"First Component"]
            );

            component(
                name: "main",
                attributes: ["custom-style"=>"color: red; background-color: black;"],
                values: [
                    "main-heading"=>"Hello",
                    "subbyheading"=>"World",
                    "extra stuff"=>$comp
                ]
            );

        ?>

    </body>
</html>