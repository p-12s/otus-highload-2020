<?php

namespace App\Controllers;

use \Core\View;
use App\Models\User;
use App\Models\Friend;
use App\Models\Utils\Post;

class Show extends \Core\Controller
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
    public function profileAction($params = [])
    {
        $user = null;
        if (isset($params['id']) && !empty($params['id'])) {
            $user = User::getUserById($params['id']);
            if ($user != null) {
                $user = $user[0];
            }
        }
        
        View::renderTemplate('show/profile.html', [
            'user' => $user
        ]);
    }
}
