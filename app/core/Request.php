<?php

class Request
{
    /**
     * HTTPメソッドがPOSTか
     * @return bool
     */
    public function isPost(): bool
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return true;
        }

        return false;
    }

    /**
     * GETリクエストの値を取得
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function getGet(string $name, string $default = null): ?string
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }

        return $default;
    }

    /**
     * POSTリクエストの値を取得
     * @param string $name
     * @param string|null $default
     * @return string|null
     */
    public function getPost(string $name, string $default = null): ?string
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }

        return $default;
    }

    /**
     * サーバーのホスト名を取得
     * @return string|null
     */
    public function getHost(): ?string
    {
        if (!empty($_SERVER['HTTP_HOST'])) {
            return $_SERVER['HTTP_HOST'];
        }

        return $_SERVER['SERVER_NAME'];
    }

    /**
     * SSL通信か
     * @return bool
     */
    public function isSsl(): bool
    {
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            return true;
        }

        return false;
    }

    /**
     * URIを取得
     * @return string
     */
    public function getRequestUri(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * ホスト部分より後ろからフロントコントローラーまでの値(ベースURL)を取得
     * @return string
     */
    public function getBaseUrl(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'];
        $requestUri = $this->getRequestUri();

        if (strpos($requestUri, $scriptName) === 0) {
            return $scriptName;
        } else if (strpos($requestUri, dirname($scriptName)) === 0) {
            return rtrim(dirname($scriptName), '/');
        }

        return '';
    }

    /**
     * GETパラメータは含まない, ベースURLより後ろの値(例: user/edit)を取得
     * @return string
     */
    public function getPathInfo(): string
    {
        $baseUrl = $this->getBaseUrl();
        $requestUri = $this->getRequestUri();

        if (($pos = strpos($requestUri, '?')) !== false) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        return (string)substr($requestUri, mb_strlen($baseUrl));
    }
}