<?php

class Router
{
    private $routes;

    public function __construct(array $definitions)
    {
        $this->routes = $this->compileRoutes($definitions);
    }

    /**
     * ルーティング定義中にコロン(:)で始まる文字列を正規表現の形式に変換して返す
     * @param array $definitions
     * @return array
     */
    public function compileRoutes(array $definitions): array
    {
        $routes = [];

        foreach ($definitions as $url => $params) {
            $tokens = explode('/', ltrim($url, '/'));
            foreach ($tokens as $i => $token) {
                if (strpos($token, ':') === 0) {
                    $name = substr($token, 1);
                    $token = "(?P<){$token}>[^/]+)";
                }
                $routes[$i] = $token;
            }

            $pattern = '/' . implode('/', $tokens);
            $routes[$pattern] = $params;
        }

        return $routes;
    }

    public function resolve(string $path_info)
    {
        if (substr($path_info, 0, 1) !== '/') {
            $path_info = '/' . $path_info;
        }

        foreach ($this->routes as $pattern => $params) {
            if (preg_match("#^{$pattern}$#", $path_info, $matches)) {
                $params = array_merge($params, $matches);
                return $params;
            }
        }

        return false;
    }
}