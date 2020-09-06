<?php

/**
 * ユーザーにまつわるリポジトリ
 */
class UserRepository extends DBRepository
{
    /**
     * ユーザー登録
     * @param string $userName
     * @param string $password
     */
    public function insert(string $userName, string $password)
    {
        $password = $this->hashPassword($password);
        $now = new DateTime();

        $sql = "
            INSERT INTO user(user_name, password, created_at)
            VALUE(:user_name, :password, :created_at)
            ";

        $stmt = $this->execute($sql, [
            ':user_name'  => $userName,
            ':password'   => $password,
            ':created_at' => $now->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * パスワードをハッシュ化
     * @param string $password
     * @return string
     */
    public function hashPassword(string $password): string
    {
        return sha1($password . 'SecretKey');
    }

    /**
     * 名前からユーザーを取得
     * @param string $userName
     * @return mixed
     */
    public function fetchByUserName(string $userName)
    {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        return $this->fetch($sql, [
            ':user_name' => $userName
        ]);
    }

    /**
     * ユーザーIDがユニークか
     * @param string $userName
     * @return bool
     */
    public function isUniqueUserName(string $userName): bool
    {
        $sql = "SELECT * FROM user WHERE user_name = :user_name";

        $row = $this->fetch($sql, [
            ':user_name' => $userName
        ]);

        // レコードがない場合はfalse
        if (!$row) {
            return true;
        }

        return false;
    }

    /**
     * フォローしているユーザーをすべて取得
     * @param int $userId
     * @return mixed
     */
    public function fetchAllFollowingByUserId(int $userId)
    {
        $sql = "
            SELECT u.*
                FROM user u
                    LEFT JOIN following f ON f.following_id = u.id
                WHERE f.user_id = :user_id
        ";

        return $this->fetchAll($sql, [':user_id' => $userId]);
    }
}