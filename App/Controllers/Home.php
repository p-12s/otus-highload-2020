<?php

namespace App\Controllers;

use App\Config;
use \Core\View;
use App\Models\User;
use App\Models\Friend;
use App\Models\Message;
use App\Models\Utils\Post;
use MySQLi;

class Home extends \Core\Controller
{
    protected function before()
    {
        parent::before();
    }

    protected function after()
    {
    }

    /**
     * Профиль юзера
     */
    public function profileAction()
    {
        $user = User::getCurrentUser();
        // echo '<pre>';print_r($user);echo '</pre>';exit();
        View::renderTemplate('home/profile.html', [
            'user' => $user[0]
        ]);
    }

    /**
     * Редактирование профиля
     */
    public function profileEditAction()
    {
        if (!empty($_POST)) {
            $errors = array();
            $country = Post::prepareUserInput($errors, $_POST['country']);
            $city = Post::prepareUserInput($errors, $_POST['city']);
            $interests = Post::prepareUserInput($errors, $_POST['interests']);

            if (!empty($errors)) {
                header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                    . '/index.php?home/profile-edit');
                return;
            }

            User::updateUserInfo($country, $city, $interests);
            header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                . '/index.php?home/profile');
            return;
        }

        $user = User::getCurrentUser();

        View::renderTemplate('home/profile-edit.html', [
            'user' => $user[0]
        ]);
    }

    /**
     * Новости друзей
     */
    public function feedAction()
    {
        $user = User::getCurrentUser();

        View::renderTemplate('home/feed.html', [
            'user' => $user
        ]);
    }

    /**
     * Друзья юзера
     */
    public function friendsAction()
    {
        $friends = Friend::getAll();

        View::renderTemplate('home/friends.html', [
            'friends' => $friends
        ]);
    }

    /**
     * Перестать дружить
     */
    public function friendRemoveAction($params = [])
    {
        if (isset($params['id']) && !empty($params['id'])) {
            Friend::remove($params['id']);
        }

        header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
            . '/index.php?home/friends');
    }

    /**
     * Начать дружить
     */
    public function friendAddAction($params = [])
    {
        if (isset($params['id']) && !empty($params['id'])) {
            Friend::add($params['id']);
        }

        header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
            . '/index.php?search/people');
    }

    /**
     * Сообщения
     */
    public function messagesAction()
    {
        echo '<pre>';print_r(989);echo '</pre>';exit();
        // тут затестить асинхронные запросы к 2 базам (на чтение которые)
        // 1 бд на чтение + 2
        $db1 = null;
        try {
            $db1 = new mysqli(Config::DB_HOST, Config::DB_USER, Config::DB_PASSWORD, Config::DB_NAME);
            $db1->set_charset('utf8');
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
        echo '<pre>';print_r($db1);echo '</pre>';exit();

        $link1 = mysqli_connect();
        $link1->query("SELECT 'test'", MYSQLI_ASYNC);
        $all_links = array($link1);
        $processed = 0;
        do {
            $links = $errors = $reject = array();
            foreach ($all_links as $link) {
                $links[] = $errors[] = $reject[] = $link;
            }
            if (!mysqli_poll($links, $errors, $reject, 1)) {
                continue;
            }
            foreach ($links as $link) {
                if ($result = $link->reap_async_query()) {
                    print_r($result->fetch_row());
                    if (is_object($result))
                        mysqli_free_result($result);
                } else die(sprintf("Ошибка MySQLi: %s", mysqli_error($link)));
                $processed++;
            }
        } while ($processed < count($all_links));



        if (!empty($_POST)) {
            if (array_key_exists('id_user_sender', $_POST) && array_key_exists('id_user_recipient', $_POST)
                && array_key_exists('message', $_POST)) {
                $message = new Message();
                $message->id_user_sender = $_POST['id_user_sender'];
                $message->id_user_recipient = $_POST['id_user_recipient'];
                $message->text = $_POST['message'];
                $message->save();
            }
            header('Location: ' . $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        }
        $user = User::getCurrentUser();
        $messages = Message::getCurrentUserMessages();
        View::renderTemplate('home/messages.html', [
            'messages' => $messages,
            'userId' => $user ? $user[0]['id'] : 0,
            'id_user_recipient' => 3
        ]);
    }
}
