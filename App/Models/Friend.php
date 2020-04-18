<?php

namespace App\Models;

use PDO;
use App\Models\User;

class Friend extends \Core\Model
{
    public static function getAll()
    {
        try {
            $user = User::getCurrentUser();
            if (!$user) {
                return null;
            }

            $db = parent::getDB();
            $stmt = $db->query('SELECT * FROM `user` WHERE id in 
                                            (SELECT f.id_friend FROM `user` AS u 
                                            INNER JOIN `friend` as f ON f.id_user = u.id
                                            WHERE u.id =' . $user[0]['id'] . ')');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function remove($userId)
    {
        try {
            $user = User::getCurrentUser();
            if (!$user) {
                return null;
            }

            $db = parent::getDB();
            $sql = 'DELETE FROM `friend` WHERE id_user=' . $user[0]['id']
                . ' AND id_friend=' . htmlspecialchars($userId);
            $db->exec($sql);

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function add($userId)
    {
        try {
            $user = User::getCurrentUser();
            if (!$user) {
                return null;
            }
            if (self::isUserFriend($user[0]['id'], htmlspecialchars($userId))) {
                return;
            }

            $db = parent::getDB();
            $sql = 'INSERT INTO `friend` VALUES (DEFAULT, ' . $user[0]['id'] . ', ' . htmlspecialchars($userId) .')';
            $db->exec($sql);

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    private static function isUserFriend($userId, $potentialFriendId)
    {
        try {
            $db = parent::getDB();
            $stmt = $db->query('SELECT * FROM `friend` WHERE id_user=' . htmlspecialchars($userId)
                . ' AND id_friend=' . htmlspecialchars($potentialFriendId));
            $response = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return (!empty($response));

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
}
