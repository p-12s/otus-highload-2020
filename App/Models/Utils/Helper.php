<?php

namespace App\Models\Utils;

class Helper
{
    public static function eraseSecretUserData($arr) {
        $erasedArr = [];
        foreach ($arr as $item) {
            $item['email'] = '***';
            $item['password'] = '***';
            $item['token'] = '***';
            array_push($erasedArr, $item);
        }
        return $erasedArr;
    }
}
