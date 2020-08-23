<?php

namespace Core;

use MySQLi;
use App\Config;

abstract class Model
{
    protected static function getDB()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        static $db;
        if (!$db) {
            try {
                $db = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
                $db->set_charset('utf8');
                return $db;

            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        return $db;
    }
    protected static function getShardedDBs()
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        static $db;
        if (!$db[1]) {
            try {
                $db[1] = new mysqli(
                    Config::$shard_1['db_host'],
                    Config::$shard_1['db_user'],
                    Config::$shard_1['db_password'],
                    Config::$shard_1['db_name']);
                $db[1]->set_charset('utf8');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
        }
        if (!$db[2]) {
            try {
                $db[2] = new mysqli(
                    Config::$shard_2['db_host'],
                    Config::$shard_2['db_user'],
                    Config::$shard_2['db_password'],
                    Config::$shard_2['db_name']);
                $db[2]->set_charset('utf8');
            } catch (\Exception $e) {
                echo $e->getMessage();
            }
            return $db;
        }
    }
    /*
    protected static function getWriteDB()
    {
        static $db;
        if (!$db) {
            try {
                $dsn = 'mysql:host='. Config::DB_HOST .';dbname='. Config::DB_NAME .';charset=utf8';
                $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $db;

            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $db;
    }
    protected static function getReadDB()
    {
        static $db;
        if (!$db) {
            try {
                $dsn = 'mysql:host='. Config::DB_HOST .';dbname='. Config::DB_NAME .';charset=utf8';
                $db = new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);

                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                return $db;

            } catch (\PDOException $e) {
                echo $e->getMessage();
            }
        }
        return $db;
    }*/
}
