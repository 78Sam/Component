<?php


$redir = $_SERVER["REQUEST_URI"];
$redir = substr($redir, strlen('/Test'));


if (isset($_GET["err"])) {
    if ($_GET["err"] == 404) {
        $redir = "/404";
    } else if ($_GET["err"] == 403) {
        $redir = "/403";
    }
}


$routes = [
    "views/home.php"=>[
        "route"=>"views/home.php"
    ],
    "views/sales.php"=>[
        "route"=>"views/sales.php"
    ],
    "views/products/iphone.php"=>[
        "route"=>"views/products/iphone.php",
        "auth"=>true,
        "fallback"=>"/sales"
    ],
    "views/404.php"=>[
        "route"=>"views/404.php",
        "auth"=>false
    ],
    "views/403.php"=>[
        "route"=>"views/403.php",
        "auth"=>false
    ],
];

$aliases = [
    "/"=>"views/home.php",
    "/index"=>"views/home.php",
    "/home"=>"views/home.php",
    "/index.php"=>"views/home.php",

    "/sales"=>"views/sales.php",

    "/products/iphone"=>"views/products/iphone.php",

    "/404"=>"views/404.php",
    "/403"=>"views/403.php",
    "default"=>"views/404.php"
];


function routeManager(string $redir) {
    global $routes;
    global $aliases;

    $redir = strtolower($redir);
    $redir = str_replace(" ", "", $redir);

    if (!isset($aliases[$redir])) {
        $redir = "default";
    }

    $route = $routes[$aliases[$redir]];

    if ($route["auth"] ?? false) { // If auth key exists and is true
        if (!(isset($_COOKIE["admin"]) && $_COOKIE["admin"] === "admin")) {
            $route = $routes[$route["fallback"]];
            header("Location: https://sam-mccormack.co.uk/Test/sales");
        }
    }

    require($route["route"]);

}


routeManager($redir);


?>