<?php

class App {

    protected $controller = 'movie';
    protected $method = 'index';
    protected $special_url = ['apply', 'movie'];
    protected $params = [];

    public function __construct() {
        if (isset($_SESSION['auth']) == 1) {
            //$this->method = 'index';
            $this->controller = 'home';
        } 

        // This will return a broken up URL
        // it will be /controller/method
        $url = $this->parseUrl();

        /* if controller exists in the URL, then go to it
         * if not, then go to this->controller which is defaulted to home 
         */

        if (isset($url[1])) {
            // Remove .php extension if present
            $controllerName = str_replace('.php', '', $url[1]);

            if (file_exists('app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                $_SESSION['controller'] = $this->controller;

                /* This is if we have a special URL in the index.
                 * For example, our apply page is public and in the index method
                 * We do not want the method to be login in this case, but instead index
                 * 
                 */
                if (in_array($this->controller, $this->special_url)) { 
                  $this->method = 'index';
                }
            }
            unset($url[1]);
        } else {
            // Default to movie controller if no valid controller is found
            $this->controller = 'movie';
        }

        require_once 'app/controllers/' . $this->controller . '.php';

        // Convert controller name to class name (e.g., 'movie' -> 'MovieController')
        $className = ucfirst($this->controller) . 'Controller';
        $this->controller = new $className;

        // check to see if method is passed
        // check to see if it exists
        if (isset($url[2])) {
            if (method_exists($this->controller, $url[2])) {
                $this->method = $url[2];
                $_SESSION['method'] = $this->method;
                unset($url[2]);
            }
        }

        // This will rebase the params to a new array (starting at 0)
        // if params exist
        $this->params = $url ? array_values($url) : [];

        // Debug: Check if controller and method exist
        if (!method_exists($this->controller, $this->method)) {
            die("Error: Method {$this->method} not found in controller " . get_class($this->controller));
        }

        call_user_func_array([$this->controller, $this->method], $this->params);		
    }

    public function parseUrl() {
        $u = "{$_SERVER['REQUEST_URI']}";
        //trims the trailing forward slash (rtrim), sanitizes URL, explode it by forward slash to get elements
        $url = explode('/', filter_var(rtrim($u, '/'), FILTER_SANITIZE_URL));
        unset($url[0]);

        // If URL is empty (root path), return empty array
        if (empty($url) || (count($url) == 1 && empty($url[1]))) {
            return [];
        }

        return $url;
    }

}
