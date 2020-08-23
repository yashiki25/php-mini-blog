<?php

abstract class DBRepository
{
    protected $con;

    public function __construct($con)
    {
        $this->setConnection($con);
    }

    /**
     * コネクションをセット
     * @param $con
     */
    public function setConnection($con): void
    {
        $this->con = $con;
    }

    /**
     * SQLを実行
     * @param string $sql
     * @param array $params
     */
    public function execute(string $sql, $params = [])
    {
        $stmt = $this->con->prepare($sql);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * 実行結果を1行のみ取得
     * @param string $sql
     * @param array $params
     */
    public function fetch(string $sql, array $params = [])
    {
        return $this->execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * 実行結果を全て取得
     * @param string $sql
     * @param array $params
     */
    public function fetchAll(string $sql, array $params = [])
    {
        return $this->execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }
}