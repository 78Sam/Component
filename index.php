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


switch ($redir) {

    case "/":
    case "/index";
    case "/home";
    case "/index.php";
        include "views/home.php";
        break;

    case "/sales":
        include "views/sales.php";
        break;

    case "/products/iphone":
        include "views/products/iphone.php";
        break;

    case "/404":
        include "views/404.php";
        break;

    case "/403":
        include "views/403.php";
        break;
    
    default:
        include "views/404.php";
        break;
}


?>