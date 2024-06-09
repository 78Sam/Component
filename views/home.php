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

        <title>Home</title>
        <link rel="icon" type="image/x-icon" href="assets/pingu.png">

        <link rel="stylesheet" href="styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script src="js/nav.js"></script>

    </head>
    <body>
        
        <?php

            $sections = [
                _component(name: "section", values: ["title"=>"COMPONENT PHP", "image"=>"test.png", "_id"=>"splash-page"]),
                _component(
                    name: "short-stack",
                    values: [
                        "image"=>"component.png",
                        "title"=>"Components",
                        "sub-heading"=>"gen component 'name'",
                        "tags"=>"<h3>Reusable</h3><h3>@Values@</h3><h3>&lt;Defaults&gt;</h3>"
                    ]
                )
            ];
        
            component(
                name: "root",
                values: ["comps"=>group($sections)]
            );
        
        ?>

    </body>
</html>