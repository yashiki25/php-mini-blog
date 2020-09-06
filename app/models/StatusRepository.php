<?php

/**
 * 投稿にまつわるリポジトリ
 * Class StatusRepository
 */
class StatusRepository extends DBRepository
{
    /**
     * 投稿を保存
     * @param int $userId
     * @param string $body
     */
    public function insert(int $userId, string $body)
    {
        $now = new DateTime();

        $sql = "
            INSERT INTO status(user_id, body, created_at)
            VALUE(:user_id, :body, :created_at)
        ";

        $stmt = $this->execute($sql, [
            ':user_id'    => $userId,
            ':body'       => $body,
            ':created_at' => $now->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * 自分とフォローしているユーザーの投稿を取得
     * @param int|null $userId
     * @return mixed
     */
    public function fetchAllPersonalArchivesByUserId(?int $userId)
    {
        $sql = "
            SELECT a.*, u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                    LEFT JOIN following f ON f.following_id = a.user_id
                        AND f.user_id = :user_id
                WHERE f.user_id = :user_id OR u.id = :user_id
                ORDER BY a.created_at DESC
        ";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    /**
     * ユーザーIDから投稿をすべて取得
     * @param int $userId
     * @return mixed
     */
    public function fetchAllByUserId(int $userId)
    {
        $sql = "
            SELECT a.*, u.user_name
                FROM status a
                    LEFT JOIN user u ON a.user_id = u.id
                WHERE u.id = :user_id
                ORDER BY a.created_at DESC
        ";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }

    /**
     * ユーザーIDと名前から投稿を取得
     * @param int $id
     * @param string $userName
     * @return mixed
     */
    public function fetchByIdAndUserName(int $id, string $userName)
    {
        $sql = "
            SELECT a.*, u.user_name
            FROM status a
                LEFT JOIN user u ON u.id = a.user_id
            WHERE a.id = :id
                AND u.user_name = :user_name 
        ";

        return $this->fetch($sql, [
            ':id'        => $id,
            ':user_name' => $userName
        ]);
    }
}