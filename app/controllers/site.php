<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Utils\Post;
use \Core\View;

class Site extends \Core\Controller
{
    protected function before()
    {
    }

    protected function after()
    {
    }

    public function logoutAction()
    {
        setcookie('FBID', '', time() - 60 * 60 * 24, '/', null, null, true);
        header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME'] . '/public/index.php?site/login');
    }

    public function loginAction()
    {
        $user = User::getCurrentUser();
        if (!empty($user)) {
            header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME'] . '/public/index.php?home/feed');
        }

        // вывод страницы авторизации/регистрации
        if (empty($_POST)) {
            View::renderTemplate('site/login.html'); // отдать шаблон профиля
            return;
        }

        // авторизация
        $errors = array();
        if (!empty($_POST['login_email']) && !empty($_POST['login_password'])) {
            $loginEmail = Post::prepareUserInput($errors, $_POST['login_email']);
            $loginPassword = Post::prepareUserInput($errors, $_POST['login_password']);

            if (!empty($errors)) {
                header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                    . '/public/index.php?site/login');
                return;
            }

            $user = User::getUserByEmail($loginEmail);
            if (!$user) {
                header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                    . '/public/index.php?site/login');
                return;
            }
            if (!password_verify($loginPassword, $user[0]['password'])) {
                header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                    . '/public/index.php?site/login');
                return;
            }
            // создать токен и обновить в бд
            $cryptoFlag = true;
            $token = bin2hex(openssl_random_pseudo_bytes(64, $cryptoFlag));
            User::updateTokenByEmail($loginEmail, $token);

            setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', null, null, true);
            header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                . '/public/index.php?home/profile');
            return;
        }

        $errors = array();
        $firstName = Post::prepareUserInput($errors, $_POST['first_name']);
        $lastName = Post::prepareUserInput($errors, $_POST['last_name']);
        $email = Post::prepareUserInput($errors, $_POST['email']);
        $password = Post::prepareUserInput($errors, $_POST['password']);
        $birthDay = Post::prepareUserInput($errors, $_POST['birth_day']);
        $birthMonth = Post::prepareUserInput($errors, $_POST['birth_month']);
        $birthYear = Post::prepareUserInput($errors, $_POST['birth_year']);
        $gender = Post::prepareUserInput($errors, $_POST['gender']);

        if (!empty($errors)) {
            header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME']
                . '/public/index.php?site/login');
            return;
        }

        $user = new User();
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT, [ 'cost' => 11 ]); //$password;
        $user->firstName = $firstName;
        $user->lastName = $lastName;
        $user->birthday = date_create(date( $birthYear .'-'. $birthMonth .'-'. $birthDay ));
        $user->country = NULL;
        $user->city = NULL;
        $user->interests = NULL;
        $user->gender = $gender;

        $cryptoStrong = true;
        $user->token = bin2hex(openssl_random_pseudo_bytes(64, $cryptoStrong));
        $user->save();

        setcookie('FBID', $user->token, time() + 60 * 60 * 24 * 7, '/', null, null, true);
        header('Location: '. $_SERVER['REQUEST_SCHEME'] . '://'. $_SERVER['SERVER_NAME'] . '/public/index.php?home/profile');
    }
}
