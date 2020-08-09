<?php

abstract class Application
{
    private bool $debug = false;
    private Request $request;
    private Response $response;
    private Session $session;
    private DBManager $dbManager;
    private Router $router;

    public function __construct(bool $debug = null)
    {
        $this->setDebugModel($debug);
        $this->initialize();
        $this->configure();
    }

    private function setDebugModel(bool $debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    private function initialize()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->dbManager = new DBManager();
        $this->router = new Router($this->registerRouter());
    }

    private function configure()
    {

    }

    abstract public function getRootDir();

    abstract protected function registerRouter();

    public function isDebugMode(): bool
    {
        return $this->debug;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getDBManager(): DBManager
    {
        return $this->dbManager;
    }

    public function getControllerDir(): string
    {
        return "{$this->getRootDir()}/controllers";
    }

    public function getViewDir(): string
    {
        return "{$this->getRootDir()}/views";
    }

    public function getModelDir(): string
    {
        return "{$this->getRootDir()}/models";
    }

    public function getWebDir(): string
    {
        return "{$this->getRootDir()}/web";
    }

    /**
     * コントローラーのアクションを呼び出し
     */
    public function run(): void
    {
        try {
            $params = $this->router->resolve($this->request->getPathInfo());

            if ($params === false) {
                throw new HttpNotFoundException("No route found for {$this->request->getPathInfo()}");
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action);
        } catch (HttpNotFoundException $e) {
            $this->render404Page($e);
        }

        $this->response->send();
    }

    /**
     * アクションを実行
     * @param string $controllerName
     * @param string $action
     * @param array $params
     */
    public function runAction(string $controllerName, string $action, array $params = []): void
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';

        $controller = $this->findController($controllerClass);
        if ($controller === false) {
            throw new HttpNotFoundException("{$controllerClass} controller is not found");
        }

        $content = $controller->run($action, $params);

        $this->response->setContent($content);
    }

    /**
     * Controllerクラスのインスタンスを取得
     * @param string $controllerClass
     * @return bool|mixed
     */
    private function findController(string $controllerClass)
    {
        if (!class_exists($controllerClass)) {
            $controllerFile = "{$this->getControllerDir()}/{$controllerClass}.php";

            if (!is_readable($controllerFile)) {
                return false;
            } else {
                require_once $controllerFile;

                if (!class_exists($controllerClass)) {
                    return false;
                }
            }
        }

        return new $controllerClass($this);
    }

    /**
     * 404画面を表示
     * @param HttpNotFoundException $e
     */
    private function render404Page(HttpNotFoundException $e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page Not Found';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(<<<EOF
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>404</title>
<body>
{$message}
</body>
</head>
</html>
EOF
);
    }
}