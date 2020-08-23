<?php

namespace App\Models;

use MySQLi;

class User extends \Core\Model
{
    public $email;
    public $password;
    public $token;
    public $firstName;
    public $lastName;
    public $birthday;
    public $country;
    public $city;
    public $interests;
    public $gender;

    public function save()
    {
        try {
            $value = 'DEFAULT, '
                .'\''. $this->email .'\','
                .'\''. $this->password .'\','
                .'\''. sha1($this->token) .'\','
                .'\''. $this->firstName .'\','
                .'\''. $this->lastName .'\','
                .'\''. $this->birthday->format('Y-m-d') .'\','
                .'\''. $this->country .'\','
                .'\''. $this->city .'\','
                .'\''. $this->interests .'\','
                .'\''. $this->gender .'\'';

            $db = parent::getDB();
            $stmt = $db->prepare("INSERT INTO `user` VALUES ($value)");
            $stmt->execute();
            $stmt->close();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getCurrentUser()
    {
        try {
            if (!isset($_COOKIE['FBID'])) {
                return null;
            }

            $db = parent::getDB();
            $token = sha1($_COOKIE['FBID']);
            $stmt = $db->prepare("SELECT * FROM `user` WHERE token ='". $token ."' LIMIT 1");
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getUserByEmail($email)
    {
        try {
            if (!isset($email) || empty($email)) {
                return null;
            }

            $db = parent::getDB();
            $stmt = $db->prepare("SELECT * FROM `user` WHERE email='". htmlspecialchars($email) ."' LIMIT 1");
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getUserById($id)
    {
        try {
            if (!isset($id) || empty($id)) {
                return null;
            }

            $db = parent::getDB();
            $stmt = $db->prepare('SELECT * FROM `user` WHERE id=' . htmlspecialchars($id));
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function getAll($limit = null)
    {
        try {
            $db = parent::getDB();
            $stmt = $limit
                ? $db->prepare('SELECT * FROM `user` ORDER BY id ASC LIMIT '. $limit)
                : $db->prepare('SELECT * FROM `user` ORDER BY id ASC');
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function updateTokenByEmail($email, $token)
    {
        try {
            if (!isset($email, $token) || empty($email)) {
                return null;
            }

            $db = parent::getDB();
            $stmt = $db->prepare("UPDATE `user` SET token='". sha1(htmlspecialchars($token))
                ."' WHERE email='". htmlspecialchars($email) ."'");
            $stmt->execute();
            $stmt->close();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function updateUserInfo($country, $city, $interests)
    {
        try {
            $user = self::getCurrentUser();
            if (!$user) {
                return;
            }

            $db = parent::getDB();
            $stmt = $db->prepare("UPDATE `user` SET country='". htmlspecialchars($country) ."',"
                ." city='". htmlspecialchars($city) ."',"
                ." interests='". htmlspecialchars($interests) ."' "
                . ' WHERE id=' . $user[0]['id']);
            $stmt->execute();
            $stmt->close();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function findUsersByName($firstName, $lastName)
    {
        try {
            $db = parent::getDB();
            $stmt = $db->prepare("SELECT * FROM `user` WHERE first_name like '".$firstName
                ."' and last_name like '".$lastName."' OR first_name like '".$lastName
                ."' and last_name like '".$firstName."'");
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
