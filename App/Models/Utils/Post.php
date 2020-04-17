<?php

namespace App\Models\Utils;

class Post
{
    public static function prepareUserInput(&$errors, $variable) {
        if (!isset($variable) || empty($variable)) {
            array_push($errors, array('field' => $variable, 'message' => 'Поле не существует или пустое'));
        }

        $variable = htmlspecialchars($variable);
        $variable = trim($variable);
        $variable = stripslashes($variable);
        return $variable;
    }
}
