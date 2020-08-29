<?php

namespace App\Models;

use MySQLi;

class Post extends \Core\Model
{
    public $id_author;
    public $text;
    public $date;

    public function save()
    {
        try {
            $value = 'DEFAULT, '
                . $this->id_author .','
                .' DEFAULT,'
                .'\''. $this->text .'\'';

            $dbs = parent::getDB();
            $stmt = $dbs->prepare("INSERT INTO `post` VALUES ($value)");
            $stmt->execute();//echo '<pre>';//print_r($stmt);echo '</pre>';exit();
            $stmt->close();

        } catch (\Exception $e) {
            echo $e->getMessage();exit();
        }
    }

    // TODO доставание будет из редиса
    public static function getCurrentUserPosts($authorId, $limit = 1000)
    {
        try {
            $dbs = parent::getDB();
            $stmt = $dbs->prepare('SELECT p.*, CONCAT(u.first_name, " ", u.last_name) AS name  FROM `post` as p'
                .' INNER JOIN `user` as u ON u.id = p.id_author'
                .' WHERE p.id_author=' . htmlspecialchars($authorId)
                .' ORDER BY p.id DESC LIMIT '. $limit);
            $stmt->execute();
            $arr = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $arr;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
