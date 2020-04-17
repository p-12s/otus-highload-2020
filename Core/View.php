<?php

namespace Core;

class View
{
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = "../App/views/$view";

        if (is_readable($file)) {
            require $file;
        }

        throw new \Exception("$file not found");
    }

    public static function renderTemplate($template, $args = [])
    {
        static $twig = null;

        if ($twig === null) {
            $loader = new \Twig\Loader\FilesystemLoader('../App/views');
            $twig = new \Twig\Environment($loader);
        }

        echo $twig->render($template, $args);
    }
}