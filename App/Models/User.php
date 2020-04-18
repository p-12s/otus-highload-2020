<?php

namespace App\Models;

use PDO;

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
            $sql = "INSERT INTO `user` VALUES ($value)";
            $db->exec($sql);

        } catch (\PDOException $e) {
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
            $stmt = $db->query("SELECT * FROM `user` WHERE token ='". $token ."' LIMIT 1");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
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
            $stmt = $db->query("SELECT * FROM `user` WHERE email='". $email ."' LIMIT 1");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
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
            $stmt = $db->query("SELECT * FROM `user` WHERE id=". $id);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function getAll()
    {
        try {
            $db = parent::getDB();
            $stmt = $db->query('SELECT * FROM `user`');
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\PDOException $e) {
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
            $sql = "UPDATE `user` SET token='". sha1(htmlspecialchars($token))
                ."' WHERE email='". htmlspecialchars($email) ."'";
            $db->exec($sql);

        } catch (\PDOException $e) {
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
            $sql = "UPDATE `user` SET country='". htmlspecialchars($country) ."',"
                ." city='". htmlspecialchars($city) ."',"
                ." interests='". htmlspecialchars($interests) ."' "
                . ' WHERE id=' . $user[0]['id'];
            $db->exec($sql);

        } catch (\PDOException $e) {
            echo $e->getMessage();
        }
    }
}
