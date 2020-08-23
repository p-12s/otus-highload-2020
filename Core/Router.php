<?php

namespace Core;

use App\Controllers\Site;

class Router
{
    protected $routes = [];
    protected $params = [];
    protected $getParams = [];

    public function add($route, $params = [])
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        $route = '/^'. $route . '/i';

        $this->routes[$route] = $params;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function getGetParams()
    {
        return $this->getParams;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function match($url)
    {
        //echo '<pre>:';print_r($url);echo '</pre>';
        //echo '<pre>$this->routes:';print_r($this->routes);echo '</pre>'; exit();

        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }

                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function dispatch($url)
    {
        /*echo '<pre>';print_r($this->routes);echo '</pre>';
        echo '<pre>';print_r($this->getParams);echo '</pre>';
        echo '<pre>';print_r($this->params);echo '</pre>';
        exit();*/
        $queryArr = $this->parseQueryString($url);
        $this->getParams = $queryArr['getParams'];

        if (!$this->match($queryArr['controllerWithAction'])) {

            throw new \Exception('No route matched', 404);
        }

        $controller = $this->params['controller'];
        $controller = $this->convertToStudlyCaps($controller);
        $controller = $this->getNamespace() . $controller;

        if (!class_exists($controller)) {
            throw new \Exception(".Class $controller not found");
        }
        $controller_object = new $controller($this->params);
        $action = $this->params['action'];
        $action = $this->convertToCamelCase($action);
        if (!is_callable([$controller_object, $action])) {
            throw new \Exception("Method $action (in controller $controller) not found");
        }

        try {
            $controller_object->$action($this->getParams);
        } catch (\Exception $e) {
            echo '<pre>';print_r($e);echo '</pre>';exit();
        }exit();
    }

    /**
     * Convert the string with hyphens to StudlyCaps
     * e.g. post-authors => PostAuthors
     * @param $string
     * @return string
     */
    protected function convertToStudlyCaps($string) {
        return str_replace(' ', '',
            ucwords(str_replace('-', ' ', $string)));
    }

    /**
     * Convert the string with hyphens to camelCase
     * e.g. add-new => addNew
     * @param $string
     * @return string
     */
    protected function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    private static function pushParam(&$arr, $str)
    {
        if (empty($str)) {
            return null;
        }
        $parts = explode('=', $str);
        if (count($parts) === 2) {
            $arr[stripslashes(trim(htmlspecialchars($parts[0])))] =
                stripslashes(trim(htmlspecialchars($parts[1])));
        }
    }
    protected function parseQueryString($url)
    {
        $result = array('controllerWithAction' => '', 'getParams' => array());
        if (!empty($url)) {
            $parts = explode('&', $url);
            $result['controllerWithAction'] = $parts[0];
            for ($i = 1, $count = count($parts); $i < $count; $i++) {
                self::pushParam($result['getParams'], $parts[$i]);
            }
        }
        return $result;
    }

    protected function getNamespace()
    {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params)) {
            $namespace .= $this->params['namespace'] . '\\';
        }
        return $namespace;
    }
}
