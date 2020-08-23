<?php

namespace App\Models;

use MySQLi;

class Message extends \Core\Model
{
    public $id_user_sender;
    public $id_user_recipient;
    public $text;
    public $date;

    public function save($shardNumber)
    {
        try {
            $value = 'DEFAULT, '
                .'\''. $this->id_user_sender .'\','
                .'\''. $this->id_user_recipient .'\','
                .'\''. $this->text .'\','
                .' DEFAULT';

            $dbs = parent::getShardedDBs();
            $stmt = $dbs[$shardNumber]->prepare("INSERT INTO `message` VALUES ($value)");
            $stmt->execute();
            $stmt->close();

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    /* заранее получить id юзера
        if (!isset($_COOKIE['FBID'])) {
            return null;
        }
        $token = sha1($_COOKIE['FBID']);
        $stmt = $db->prepare("SELECT id FROM `user` WHERE token ='". $token ."' LIMIT 1");
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
     * */
    // получение сообщений из обоих шард
    public static function getCurrentUserMessages($userId, $limit = 100)
    {
        try {
            $queryResult = array();
            $dbs = parent::getShardedDBs();
            $dbs[1]->query('SELECT * FROM `message` WHERE id_user_sender=' . $userId
                . ' OR id_user_recipient=' . $userId . ' ORDER BY date ASC', MYSQLI_ASYNC);
            $dbs[2]->query('SELECT * FROM `message` WHERE id_user_sender=' . $userId
                . ' OR id_user_recipient=' . $userId . ' ORDER BY date ASC', MYSQLI_ASYNC);
            $shardQueries = array($dbs[1], $dbs[2]);
            $processed = 0;
            do {
                $links = $errors = $reject = array();
                foreach ($shardQueries as $query) {
                    $links[] = $errors[] = $reject[] = $query;
                }
                if (!mysqli_poll($links, $errors, $reject, 1)) {
                    continue;
                }
                foreach ($links as $query) {
                    if ($result = $query->reap_async_query()) {
                        array_push($queryResult, $result->fetch_row());
                        if (is_object($result)) {
                            mysqli_free_result($result);
                        }
                    } else die(sprintf("Ошибка MySQLi: %s", mysqli_error($query)));
                    $processed++;
                }
            } while ($processed < count($shardQueries));
            return $queryResult;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
