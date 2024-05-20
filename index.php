<?php

// Suppress error reporting

error_reporting(0);

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Requirements

// start old

// // require_once($path . "dir.php");
// require_once(__DIR__ . "/dir.php");
// require_once($REQUIRE_ROUTES);
// require_once($REQUIRE_SESSIONS);

// // Route Manager

// $route_manager = new Route();

// // Register Routes

// $route_manager->registerRoute(
//     aliases: ["", "index", "home"],
//     route: "home.php"
// );

// $route_manager->registerRoute(
//     aliases: ["login", "signin", "sign%20in"],
//     route: "login.php"
// );

// $route_manager->registerRoute(
//     aliases: ["staff", "tryme"],
//     route: "staff.php",
//     options: [
//         "auth"=>true,
//         "fallback"=>"login"
//     ]
// );

// // Resolve Routes

// $route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

// // print_r($route);

// if ($route !== null) {

//     if (isset($route["auth"]) && $route["auth"]) {
//         if (!checkAuth()) {
//             header("Location: " . $URL_HOME . $route["fallback"]);
//             exit();
//         }
//     }

//     require_once($route["path"]);

// } else {
//     echo "<h1>Error</h1>";
// }

// end old

require_once(__DIR__ . "/dir.php");
// require_once(__DIR__ . "/middleware/middleware.php");
require_once($REQUIRE_DATABASE);
// require_once($REQUIRE_ROUTES);
// require_once(__DIR__ . "/routenew.php");
require_once(__DIR__ . "/route.php");

require_once(__DIR__ . "/middleware/mw_auth.php");
require_once(__DIR__ . "/middleware/mw_login.php");


$home_page = new Route(
    aliases: ["/home", "/", "/index"],
    path: "home.php"
);

$staff_page = new Route(
    aliases: ["/staff"],
    path: "staff.php",
    middleware: [
        new Auth(
            expiry: 60,
            target: "/staff",
            uses: new Login(target: "/staff", fallback: "/login", db: new Database())
        )
    ]
);

$login_page = new Route(
    aliases: ["/login"],
    path: "login.php"
);

$routes = [
    $home_page,
    $staff_page,
    $login_page
];

// echo $_SERVER["REQUEST_URI"];

foreach ($routes as $route) {
    if ($route->isRoute($_SERVER["REQUEST_URI"])) {
        include_once("views/" . $route->use());
        // $route->use();
    }
}

?>