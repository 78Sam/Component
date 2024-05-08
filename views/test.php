<?php

require_once("dir.php");
require_once($REQUIRE_DATABASE);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>

        <h1>Connection tester</h1>

        <?php
        
            $db = new Database();

            if ($link = $db->getConnection()) {
                echo "Connected";
            } else {
                echo "Not connected";
            }

            echo "Helloi";
        
        ?>

        <h2>End</h2>
    </body>
</html>