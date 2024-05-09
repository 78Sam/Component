# Component PHP

Component PHP designed by [@78Sam](https://github.com/78Sam/). View the official site [here](https://sam-mccormack.co.uk/phpsite).

## QuickStart

### Create a view

Using the provided generator, create a view based on the schematic provided in components/schematics/view.txt with the following command.

```cmd
python3 generate.py view home
```

This will create a view called home within the /views/ folder. Views are the pages that users will see when navigating through your site.

### Register a route

In order to allow users access to these views, we need to register routes users can take. In the file index.php we have a class called RouteManager, this allows us to register aliases for these views by which users can search for them.

Lets register the newly created home view.

>index.php
```php
<?php

// Suppress error reporting

error_reporting(0);

// Requirements

require_once($_SERVER["DOCUMENT_ROOT"] . "/Test/dir.php");
require_once($REQUIRE_ROUTES);

// Route Manager

$route_manager = new Route();

// Register Routes

$route_manager->registerRoute(
    aliases: ["", "index", "index.php", "home"],
    route: "home.php"
);

// Resolve Routes

$route = $route_manager->resolveRoute($_SERVER["REQUEST_URI"]);

if ($route !== null) {

    if (isset($route["auth"]) && $route["auth"]) {
        if (!checkAuth()) {
            header("Location: " . $URL_HOME . $route["fallback"]);
            exit();
        }
    }

    require_once($route["path"]);

} else {
    echo "<h1>Error</h1>";
}

?>
```

Now that we have registered the route with route manager, anytime a user attempts to query "", "index", "index.php" or "home" on your site (the aliases), it will provide the route "home.php" (route).