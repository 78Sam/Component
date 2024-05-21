<?php

// Suppress error reporting

error_reporting(0);

require_once(__DIR__ . "/dir.php");
require_once($REQUIRE_DATABASE);
require_once($REQUIRE_ROUTES);

require_once(__DIR__ . "/middleware/mw_auth.php");
require_once(__DIR__ . "/middleware/mw_login.php");
require_once(__DIR__ . "/middleware/mw_register.php");


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
    path: "login.php",
    middleware: [
        new Register(
            target: "/staff",
            fallback: "/register",
            db: new Database()
        )
    ]
);

$register_page = new Route(
    aliases: ["/register"],
    path: "register.php"
);

$routes = [
    $home_page,
    $staff_page,
    $login_page,
    $register_page
];

// echo $_SERVER["REQUEST_URI"];

foreach ($routes as $route) {
    if ($route->isRoute($_SERVER["REQUEST_URI"])) {
        include_once("views/" . $route->use());
        // $route->use();
    }
}

?>