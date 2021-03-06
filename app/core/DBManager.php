<?php

class DBManager
{
    private array $connections = [];
    private array $repositoryConnectionMap = [];
    private array $repositories = [];

    public function __destruct()
    {
        // Repositoryクラスのインスタンスを破棄
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

        // PDOのインスタンスを破棄
        foreach ($this->connections as $connection) {
            unset($connection);
        }
    }

    /**
     * PDOクラスのインスタンスを作成
     * @param $name
     * @param $params
     */
    public function connect(string $name, array $params): void
    {
        $params = array_merge([
            'dsn'      => null,
            'user'     => '',
            'password' => '',
            'options'  => [],
        ], $params);

        $con = new PDO(
            $params['dsn'],
            $params['user'],
            $params['password'],
            $params['options']
        );

        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connections[$name] = $con;
    }

    /**
     * DBコネクションを取得
     * @param string|null $name
     * @return mixed
     */
    public function getConnection(string $name = null)
    {
        if (is_null($name)) {
            return current($this->connections);
        }

        return $this->connections[$name];
    }

    /**
     * 指定リポジトリのコネクションを取得
     * @param string $repositoryName
     */
    public function getConnectionForRepository(string $repositoryName)
    {
        if (isset($this->repositoryConnectionMap[$repositoryName])) {
            $name = $this->repositoryConnectionMap[$repositoryName];
            return $this->getConnection($name);
        } else {
            return $this->getConnection();
        }
    }

    /**
     * 指定リポジトリのインスタンスを取得
     * @param string $repositoryName
     * @return mixed
     */
    public function get(string $repositoryName)
    {
        if (!isset($this->repositories[$repositoryName])) {
            $repositoryClass = "{$repositoryName}Repository";
            $con = $this->getConnectionForRepository($repositoryName);

            $repository = new $repositoryClass($con);

            $this->repositories[$repositoryName] = $repository;
        }

        return $this->repositories[$repositoryName];
    }
}