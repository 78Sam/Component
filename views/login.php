<?php

// Dir

require_once($_SERVER["DOCUMENT_ROOT"] . "/Test/dir.php");

// Extra requirements

require_once($REQUIRE_COMPONENTS);
require_once($REQUIRE_DATABASE);

// DB Connection

// $db = new Database();

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login</title>
    </head>
    <body>
        <?php
            if (isset($_GET["err"])) {
                echo "<h3>" . $_GET["err"] . "</h3>";
            }
            component(name: "login-form", attributes: ["action"=>"services/loginservice.php"]);
        ?>
    </body>
</html>