<?php

// Dir

require_once("dir.php");

// Extra requirements

require_once($REQUIRE_COMPONENTS);
require_once($REQUIRE_DATABASE);

// DB Connection

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

        <link rel="stylesheet" href="styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    </head>
    <body>

        <h1>Home</h1>

        <img src="assets/pingu.png" alt="">

        <?php
        
            component(name: "test");

        ?>

    </body>
</html>