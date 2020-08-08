<?php

class Response
{
    private $content;
    // TODO: ステータスコードとテキストを別ファイルに定義
    private $statusCode = 200;
    private $statusText = 'OK';
    private $httpHeaders = [];

    /**
     * レスポンス送信
     */
    public function send(): void
    {
        header("HTTP/1.1{$this->statusCode} {$this->statusText}");

        foreach ($this->httpHeaders as $name => $value) {
            header("{$name}: {$value}");
        }

        // 送信
        echo $this->content;
    }

    /**
     * HTMLなどクライアントに返すコンテンツをセット
     * @param $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * ステータスコードをセット
     * @param int $statusCode
     * @param string $statusText
     */
    public function setStatusCode(int $statusCode, string $statusText): void
    {
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
    }

    /**
     * HTTPヘッダをセット
     * @param $name
     * @param $value
     */
    public function setHttpHeader($name, $value): void
    {
        $this->httpHeaders[$name] = $value;
    }
}