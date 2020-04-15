<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

// Autoloader
spl_autoload_register( function ($class) {
    $root = dirname(__DIR__);
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_readable($file)) {
        require $root . '/' . str_replace('\\', '/', $class) . '.php';
    }
});

// Error and Exception handlering
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

// Routing
$router = new Core\Router();

//$router->add('', ['controller' => 'home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'admin']);

$router->dispatch($_SERVER['QUERY_STRING']);
