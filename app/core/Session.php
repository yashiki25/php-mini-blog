<?php

class Session
{
    private static bool $sessionStarted = false;
    private static bool $sessionIdRegenerated = false;

    public function __construct()
    {
        if (!self::$sessionStarted) {
            session_start();

            self::$sessionStarted = true;
        }
    }

    /**
     * セッションをセット
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void
    {
        $_SESSION[$name] = $value;
    }

    /**
     * セッションを取得
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function get(string $name, $default = null)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

        return $default;
    }

    /**
     * セッションを除去
     * @param string $name
     */
    public function remove(string $name): void
    {
        unset($_SESSION[$name]);
    }

    /**
     * セッションをすべてクリア
     */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * セッションIDを新規発行
     * @param bool $destroy
     */
    public function regenerate($destroy = true): void
    {
        if (!self::$sessionIdRegenerated) {
            session_regenerate_id($destroy);

            self::$sessionIdRegenerated = true;
        }
    }

    /**
     * ログインセッションを保存
     * @param bool $bool
     */
    public function setAuthenticated(bool $bool): void
    {
        $this->set('_authenticated', (bool)$bool);
        $this->regenerate();
    }

    /**
     * ログインしているか
     * @return mixed|null
     */
    public function isAuthenticated()
    {
        return $this->get('_authenticated', false);
    }
}