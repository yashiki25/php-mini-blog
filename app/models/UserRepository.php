<?php

class UserRepository extends DBRepository
{
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

    public function hashPassword(string $password): string
    {
        return sha1($password . 'SecretKey');
    }

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
}