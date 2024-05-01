<?php

$redir = $_SERVER["REQUEST_URI"];

$redir = substr($redir, strlen('/Test/'));

if ($redir === "peepee") {
    include "views/view1.php";
} else if ($redir === "poopoo") {
    include "index.php";
} else {
    include "views/404.php";
}


?>