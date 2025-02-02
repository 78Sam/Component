<?php


class Route {

    private array $aliases;
    private string $path;
    private array $middleware;

    public function __construct(array $aliases, string $path, array $middleware=[]) {
        $this->aliases = array_fill_keys($aliases, true);
        $this->path = $path;
        $this->middleware = $middleware;
    }

    public function getAliases(): array {
        return $this->aliases;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getMiddleware(): array {
        return $this->middleware;
    }

    public function isRoute(string $request): bool {
        $pattern = "/[?]{1}([a-zA-Z0-9%_]+[=]{1}[a-zA-Z0-9%_]+[&]{1})*([a-zA-Z0-9%_]+[=]{1}[a-zA-Z0-9%_]+){1}/";
        $res = preg_split($pattern, $request);

        if ($res) {$request = $res[0];}

        return isset($this->aliases[$request]);
    }

    public function use(): string {
        foreach ($this->middleware as $mw) {
            if ($mw instanceof Middleware) {
                $mw->apply();
            }
        }
        return $this->path;
    }

}


?>