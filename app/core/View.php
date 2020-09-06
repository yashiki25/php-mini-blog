<?php

class View
{
    private string $baseDir;
    private array $defaults;
    private array $layoutVariable = [];

    public function __construct(string $baseDir, array $defaults = [])
    {
        $this->baseDir = $baseDir;
        $this->defaults = $defaults;
    }

    public function setLayoutVar(string $name, string $value): void
    {
        $this->layoutVariable[$name] = $value;
    }

    public function render(string $_path, array $_variables = [], string $_layout = null)
    {
        $_file = "{$this->baseDir}/{$_path}.php";

        extract(array_merge($this->defaults, $_variables));

        ob_start();
        ob_implicit_flush(0);

        require $_file;

        $content = ob_get_clean();

        if (isset($_layout)) {
            $content = $this->render($_layout,
                array_merge($this->layoutVariable, [
                    '_content' => $content,
                ])
            );
        }

        return $content;
    }

    public function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}