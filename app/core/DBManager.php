<?php

class DBManager
{
    private array $connections = [];
    private array $repositoryConnectionMap = [];
    private array $repositories = [];

    public function __destruct()
    {
        foreach ($this->repositories as $repository) {
            unset($repository);
        }

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
     * @return PDO
     */
    public function getConnection(string $name = null): PDO
    {
        if (is_null($name)) {
            return current($this->connections);
        }

        return $this->connections[$name];
    }

    /**
     * 指定リポジトリのコネクションを取得
     * @param string $repositoryName
     * @return PDO
     */
    public function getConnectionForRepository(string $repositoryName): PDO
    {
        if (isset($this->repositoryConnectionMap[$repositoryName])) {
            $name = $this->repositoryConnectionMap[$repositoryName];
            return $this->getConnection($name);
        } else {
            return $this->getConnection();
        }
    }

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