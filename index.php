<?php

require_once("route.php");

$route_manager = new Route();

$route_manager->registerRoute(
    aliases: ["", "index", "index.php", "home"],
    route: "home.php"
);

$route_manager->registerRoute(
    aliases: ["sales"],
    route: "sales.php"
);

$route_manager->registerRoute(
    aliases: ["products/iphone"],
    route: "products/iphone.php",
    options: [
        "auth"=>true,
        "fallback"=>"/sales"
    ]
);

$route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

if ($route !== null) {
    require_once($route["path"]);
} else {
    echo "<h1>Error</h1>";
    // print_r($route_manager->getRoutes());
}

?>