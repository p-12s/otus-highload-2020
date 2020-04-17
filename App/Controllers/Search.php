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
    public function peopleAction()
    {
        $users = User::getAll();

        View::renderTemplate('search/people.html', [
            'users' => $users
        ]);
    }
}
