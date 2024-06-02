<?php

require_once(__DIR__ . "/../dir.php");
require_once($REQUIRE_COMPONENTS);
require_once($REQUIRE_DATABASE);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <base href="/">

        <title>register</title>
        <link rel="icon" type="image/x-icon" href="assets/pingu.png">

        <link rel="stylesheet" href="styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    </head>
    <body>
        
        <?php
        
            if (isset($_GET["err"])) {
                echo "<h2>" . $_GET["err"] . "</h2>";
            }
        
            component(
                name: "login",
                attributes: [
                    "action"=>"login",
                    "method"=>"POST"
                ],
                values: [
                    "submit-name"=>"Register"
                ]
            )
        
        ?>

    </body>
</html>