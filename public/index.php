<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';


spl_autoload_register( function ($class) {
    $root = dirname(__DIR__);
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';

	if (is_readable($file)) {
        require $file;
    } else {
		throw new \Exception($file .' - file can not be read');
	}
});

// Error and Exception handlering
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

$router = new Core\Router();


$router->add('{controller}/{action}');


$router->dispatch($_SERVER['QUERY_STRING']);
