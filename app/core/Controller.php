<?php

abstract class Controller
{
    private string $controllerName;
    private string $actionName;
    protected Application $application;
    protected Request $request;
    protected Response $response;
    protected Session $session;
    protected DBManager $dbManager;

    /**
     * 認証が必要なアクション
     * @var array
     */
    protected $authActions = [];

    public function __construct(Application $application)
    {
        $this->controllerName = strtolower(substr(get_class($this), 0, -10));

        $this->application = $application;
        $this->request = $application->getRequest();
        $this->response = $application->getResponse();
        $this->session = $application->getSession();
        $this->dbManager = $application->getDBManager();
    }

    /**
     * アクション実行
     * @param string $action
     * @param array $params
     * @return mixed
     * @throws HttpNotFoundException
     */
    public function run(string $action, array $params = [])
    {
        $this->actionName = $action;

        $actionMethod = "{$action}Action";
        if (!method_exists($this, $actionMethod)) {
            $this->forward404();
        }

        // ログイン状態のみ有効なアクションを制御
        if ($this->needsAuthentication($action) && !$this->session->isAuthenticated()) {
            throw new UnauthorizedActionException();
        }

        $content = $this->$actionMethod($params);

        return $content;
    }

    /**
     * 認証が必要なアクションか
     * @param string $action
     * @return bool
     */
    private function needsAuthentication(string $action): bool
    {
        if ($this->authActions === true
        || (is_array($this->authActions) && in_array($action, $this->authActions))) {
            return true;
        }

        return false;
    }

    /**
     * ビューファイルの読み込み
     * @param array $variables
     * @param string|null $template
     * @param string $layout
     * @return false|string
     */
    protected function render(array $variables = [], string $template = null, string $layout = 'layout')
    {
        $default = [
            'request'  => $this->request,
            'base_url' => $this->request->getBaseUrl(),
            'session'  => $this->session,
        ];

        $view = new View($this->application->getViewDir(), $default);

        if (is_null($template)) {
            $template = $this->actionName;
        }

        $path = "{$this->controllerName}/{$template}";

        return $view->render($path, $variables, $layout);
    }

    /**
     * 404画面に遷移
     * @throws HttpNotFoundException
     */
    public function forward404()
    {
        throw new HttpNotFoundException("Forwarded 404 page from {$this->controllerName}/{$this->actionName}");
    }

    /**
     * 任意のURLにリダイレクト
     * @param string $url
     */
    protected function redirect(string $url)
    {
        if (!preg_match('#https?://#', $url)) {
            $protocol = $this->request->isSsl() ? 'https://' : 'http://';
            $host = $this->request->getHost();
            $baseUrl = $this->request->getBaseUrl();

            $url = "{$protocol}{$host}{$baseUrl}{$url}";
        }

        $this->response->setStatusCode(302, 'Found');
        $this->response->setHttpHeader('Location', $url);
    }

    /**
     * CSRFトークンを作成
     * @param string $formName
     * @return string
     */
    protected function generateCsrfToken(string $formName): string
    {
        $key = "csrf_tokens/{$formName}";
        $tokens = $this->session->get($key, []);

        // 最大10個のトークンを保持
        if (count($tokens) >= 10) {
            array_shift($tokens);
        }

        $token = sha1($formName . session_id() . microtime());
        $tokens[] = $token;

        $this->session->set($key, $tokens);

        return $token;
    }

    /**
     * リクエストとセッションのCSRFトークンが一致しているか確認
     * @param string $formName
     * @param string $token
     * @return bool
     */
    protected function checkCsrfToken(string $formName, string $token): bool
    {
        $key = "csrf_tokens/{$formName}";
        $tokens = $this->session->get($key, []);

        if (false !== ($pos = array_search($token, $tokens, true))) {
            unset($tokens[$pos]);
            $this->session->set($key, $tokens);

            return true;
        }

        return false;
    }
}