<?php


// $path = "";
// while (!file_exists($path . "dir.php")) {
//     $path = $path . "../";
// }
// require_once($path . "dir.php");
require_once(__DIR__ . "/../dir.php");
require_once($REQUIRE_COMPONENTS);
require_once($REQUIRE_DATABASE);


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>home</title>
        <link rel="icon" type="image/x-icon" href="assets/pingu.png">

        <link rel="stylesheet" href="styles/main.css">

        <script src="https://kit.fontawesome.com/8fd25e8e0f.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    </head>
    <body>
        
        <?php
            
            $db = new Database();

            if ($db->connectionStatus()) {
                $users = [];
                foreach ($db->query(query: "getUserStatement", query_params: [["key"=>"email", "value"=>"Sam@gmail.com"]]) as $row) {
                    $users[] = _component(
                        name: "user",
                        values: ["email"=>$row["email"]]
                    );
                }
            }

            component(
                name: "welcome",
                attributes: ["custom-style"=>"justify-content:center; align-items:center;"],
                values: [
                    "title"=>"Welcome!",
                    "paragraph"=>"This site is made with Component PHP",
                    "users"=>group($users)
                ]
            );
        
        ?>

    </body>
</html>