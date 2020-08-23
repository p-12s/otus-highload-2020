<?php

namespace App\Controllers;

use \Core\View;
use App\Models\User;

class Search extends \Core\Controller
{
    protected function before()
    {
        parent::before();
    }

    protected function after()
    {
    }

    /**
     * Друзья
     */
    public function peopleAction($params = [])
    {
        $searchResult = isset($params['s']) ? $params['s'] : 'nope';

        // если поиск не пришел - выводим 20
        // иначе - ищем пришедший ключ
        $users = User::getAll(20);

        View::renderTemplate('search/people.html', [
            'users' => $users,
            'searchResult' => $searchResult
        ]);
    }
}
