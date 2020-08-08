<?php

class Response
{
    private $content;
    // TODO: ステータスコードとテキストを別ファイルに定義
    private $statusCode = 200;
    private $statusText = 'OK';
    private $httpHeaders = [];

    public function send(): void
    {
        header("HTTP/1.1{$this->statusCode} {$this->statusText}");

        foreach ($this->httpHeaders as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }

    public function setContent($content): void
    {
        $this->content = $content;
    }

    public function setStatusCode(int $statusCode, string $statusText): void
    {
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
    }

    public function setHttpHeader($name, $value)
    {
        $this->httpHeaders[$name] = $value;
    }
}