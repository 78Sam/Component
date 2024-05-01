<?php

$redir = $_GET["redir"];

if ($redir === "index" || $redir === "index.php") {
    // header("Location: https://sam-mccormack.co.uk/Test/index.php?r=1&from=" . $redir);
    header("Location: https://sam-mccormack.co.uk/Test/index.php?r=1");
} else {
    // header("Location: https://sam-mccormack.co.uk/Test/index.php?r=1&from=" . $redir);
    header("Location: https://sam-mccormack.co.uk/Test/index.php?r=1");
}

?>