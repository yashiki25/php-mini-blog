<?php

class ClassLoader
{
    private array $dirs = [];

    /**
     * オートロードクラスを登録する
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * ディレクトリを登録する
     * @param string $dir
     */
    public function registerDir(string $dir): void
    {
        $this->dirs[] = $dir;
    }

    /**
     * クラスファイルを読み込む
     * @param string $class
     */
    public function loadClass(string $class): void
    {
        foreach ($this->dirs as $dir) {
            $file = "{$dir}/{$class}.php";
            if (is_readable($file)) {
                require $file;

                return;
            }
        }
    }
}