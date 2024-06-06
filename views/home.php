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

        <title>home</title>
        <link rel="icon" type="image/x-icon" href="assets/pingu.png">

        <link rel="stylesheet" href="styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    </head>
    <body>
        
        <?php

            $sections = [
                _component(name: "section", values: ["title"=>"COMPONENT PHP", "image"=>"test.png"]),
                _component(name: "section", values: ["title"=>"REUSABILITY", "image"=>"test.png"])
            ];
        
            component(
                name: "root",
                values: ["comps"=>group($sections)]
            );
        
        ?>

    </body>
</html>