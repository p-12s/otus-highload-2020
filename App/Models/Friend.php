<?php

namespace App\Models;

use MySQLi;
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
            $stmt = $db->prepare('SELECT * FROM `user` WHERE id in 
                                            (SELECT f.id_friend FROM `user` AS u 
                                            INNER JOIN `friend` as f ON f.id_user = u.id
                                            WHERE u.id =' . $user[0]['id'] . ')');
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;

        } catch (\Exception $e) {
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
            $stmt = $db->prepare('DELETE FROM `friend` WHERE id_user=' . $user[0]['id']
                . ' AND id_friend=' . htmlspecialchars($userId));
            $stmt->execute();
            $stmt->close();
        } catch (\Exception $e) {
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
            $stmt = $db->prepare('INSERT INTO `friend` VALUES (DEFAULT, ' . $user[0]['id']
                . ', ' . htmlspecialchars($userId) .')');
            $stmt->execute();
            $stmt->close();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    private static function isUserFriend($userId, $potentialFriendId)
    {
        try {
            $db = parent::getDB();
            $stmt = $db->prepare('SELECT * FROM `friend` WHERE id_user=' . htmlspecialchars($userId)
                . ' AND id_friend=' . htmlspecialchars($potentialFriendId));
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return (!empty($arr));

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
