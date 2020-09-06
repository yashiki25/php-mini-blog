<?php

/**
 * フォローにまつわるリポジトリ
 */
class FollowingRepository extends DBRepository
{
    /**
     * 対象ユーザーをフォローする
     * @param int $userId
     * @param int $followingId
     */
    public function insert(int $userId, int $followingId)
    {
        $sql = "INSERT INTO following VALUES (:user_id, :following_id)";

        $stmt = $this->execute($sql, [
            ':user_id'      => $userId,
            ':following_id' => $followingId,
        ]);
    }

    /**
     * 対象ユーザーをフォローしているか
     * @param int $userId
     * @param int $followingId
     * @return bool
     */
    public function isFollowing(int $userId, int $followingId)
    {
        $sql = "
            SELECT COUNT(user_id) as count
                FROM following 
                WHERE user_id = :user_id
                    AND following_id = :following_id
        ";

        $row = $this->fetch($sql, [
            ':user_id'      => $userId,
            ':following_id' => $followingId,
        ]);

        if ($row['count'] !== '0') {
            return true;
        }

        return false;
    }
}