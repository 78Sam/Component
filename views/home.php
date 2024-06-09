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

        <script src="js/nav.js"></script>

    </head>
    <body>
        
        <?php

            $sections = [
                _component(name: "section", values: ["title"=>"COMPONENT PHP", "image"=>"test.png", "_id"=>"splash-page"]),
                _component(
                    name: "3-col",
                    values: [
                        "image"=>"component.png",
                        "title"=>"Components",
                        "sub-heading"=>"gen component 'name'",
                        "sec-1"=>"<h2>The Skinny</h2><br><p>Components are the core way to create and organise content that will be visible on your site. Using the above command, a component and its corresponding stylesheet will be created. Components, like every other type of generable element are created based off your schematic within the schematics folder.</p>",
                        "sec-2"=>"<h2>Values</h2><br><p>Components are able to store placeholder values to be hydrated at runtime. These values are denoted by a wrapping set of @'s, such as @myvalue@.</p>",
                        "sec-3"=>"<h2>Defaults</h2><br><p>Components themselves are able to use other components by adding a HTML tag that specifies the components name, such as &lt;component-section>&lt;/component-section> these components values are then able to be hydrated at the same time as the calling component.</p>"
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