<?php

namespace App\Controllers;

use \Core\View;
use App\Models\User;
use App\Models\Friend;
use App\Models\Utils\Post;

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
                    . '/public/index.php?home/profile-edit');
                return;
            }

            User::updateUserInfo($country, $city, $interests);
            header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                . '/public/index.php?home/profile');
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
            . '/public/index.php?home/friends');
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
            . '/public/index.php?search/people');
    }
}
