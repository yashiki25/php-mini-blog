<?php

abstract class DBRepository
{
    private PDO $con;

    public function __construct(PDO $con)
    {
        $this->setConnection($con);
    }

    /**
     * コネクションをセット
     * @param PDO $con
     */
    public function setConnection(PDO $con): void
    {
        $this->con = $con;
    }

    /**
     * SQLを実行
     * @param string $sql
     * @param array $params
     * @return bool|PDOStatement
     */
    public function execute(string $sql, array $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * 実行結果を1行のみ取得
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetch(string $sql, array $params = []): array
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 実行結果を全て取得
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}