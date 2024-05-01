<?php

// Remove if not required, security risk

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base requirement 

require("dir.php");

// Extra requirements

require($REQUIRE_COMPONENTS);
require($REQUIRE_DATABASE);

// 

$db = new Database();

?>

<!DOCTYPE html>
<html lang="en">
    <head>

        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <base href="/Test/">

        <title>Test</title>
        <link rel="icon" type="image/x-icon" href="assets/pingu.png">

        <link rel="stylesheet" href="/Test/styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    </head>
    <body>

        <h1>Predicted Sales</h1>

        <a href="https://sam-mccormack.co.uk/Test/products/iphone">iphone!</a>

        <?php
        
            component(name: "test");

        ?>

    </body>
</html>