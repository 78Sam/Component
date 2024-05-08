<?php


class Route {

    private array $routes;
    private array $aliases;

    public function __construct() {
        
        $this->routes = [];
        $this->aliases = [];

        if (file_exists(__DIR__ . "/views/404.php")) {
            $this->registerRoute(
                aliases: ["404"],
                route: "404.php"
            );
        }

        if (file_exists(__DIR__ . "/views/403.php")) {
            $this->registerRoute(
                aliases: ["403"],
                route: "403.php"
            );
        }

    }

    /**
     * 
     * @param array $aliases The URLs that map to this route
     * @param string $route The path to the view in the views folder (not including /view/ e.g. home.php)
     * @param array $options Options: {'auth'=>bool, 'fallback'=>alias}
     * 
     */
    public function registerRoute(array $aliases, string $route, array $options=null) {

        foreach ($aliases as $alias) {
            $this->aliases[$alias] = $route;
        }

        $path = __DIR__ . "/views/" . $route;

        if (!file_exists($path)) {
            return;
        }

        if (!$options) {

            $options = [
                "path"=>$path,
                "auth"=>false,
                "fallback"=>"/"
            ];

        } else {

            $options["path"] = $path;

            if (!isset($options["auth"])) {
                $options["auth"] = false;
            } else {
                $options["auth"] = (bool) $options["auth"];
            }

            if (!(isset($options["fallback"]) && $options["fallback"] !== "")) {
                $options["fallback"] = "/";
            }

        }

        $this->routes[$route] = $options;

    }

    public function resolveRoute($alias): array|null {

        $alias = substr($alias, strlen("/Test/"));

        if (str_contains($alias, "?")) {
            $query_start = strpos($alias, "?");
            $alias = substr($alias, 0, $query_start);
        }

        if (
            isset($_GET["err"]) && 
            array_key_exists($_GET["err"], $this->aliases)
        ) {
            $alias = (string) $_GET["err"];
        }

        if (array_key_exists($alias, $this->aliases)) {
            return $this->routes[$this->aliases[$alias]];
        } else if (array_key_exists("404", $this->aliases)) {
            return $this->routes[$this->aliases["404"]];
        }

        return null;

    }

    public function getRoutes(): array {
        return $this->routes;
    }

}




?>