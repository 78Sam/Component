<?php

// For testing only

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Suppress error reporting

error_reporting(0);

// Base requirements

require_once(__DIR__ . "/../dir.php");
require_once($REQUIRE_DATABASE);
require_once($REQUIRE_ROUTES);

// Middleware requirements

require_once($MIDDLEWARE_FOLDER . "authenticate.php");
require_once($MIDDLEWARE_FOLDER . "login.php");
require_once($MIDDLEWARE_FOLDER . "register.php");

// Routes

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
            target: "/public/staff",
            uses: new Login(target: "/public/staff", fallback: "/public/login", db: new Database())
        )
    ]
);

$login_page = new Route(
    aliases: ["/login"],
    path: "login.php",
    middleware: [
        new Register(
            target: "/public/staff",
            fallback: "/public/register",
            db: new Database()
        )
    ]
);

$register_page = new Route(
    aliases: ["/register"],
    path: "register.php"
);

// route-placeholder

$routes = [
    $home_page,
    $staff_page,
    $login_page,
    $register_page,
    // routes-placeholder

];

foreach ($routes as $route) {
    if ($route->isRoute($_SERVER["REQUEST_URI"])) {
        $path = __DIR__ . "/../views/" . $route->use();
        include_once($path);
    }
}

?>