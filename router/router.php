<?php

class Route{

    private static Array $routes = [];
    private static Array $names = [];
    private static Array $middlewares = [];

    private String $route;
    private static String $baseUrl = 'localhost';

    private Array $controller;


    private function __construct(String $route, Array $controller, String $method){

        $this->route = $route;
        $this->controller = $controller;
        $this->method = $method;

        Route::$routes[$method][$this->route] = $this->controller;
    }


    /**
     * HTTP METHODS
     */
    public static function get(String $route, Array $controller ){   
        return new Route($route, $controller, 'GET');
    }

    public static function post(String $route, Array $controller ){
        return new Route($route, $controller, 'POST');
    }

    public static function delete(String $route, Array $controller ){
        return new Route($route, $controller, 'DELETE');
    }

    public static function put(String $route, Array $controller ){
        return new Route($route, $controller, 'PUT');
    }
    

    /**
     * Name routes
     */
    public function name(String $name){
        Route::$names[$name] = $this->route;
        return $this;
    }

    public static function getByName(String $name, Array $params = []){
        if(isset(Route::$names[$name])){
            $route = Route::$baseUrl.Route::$names[$name];
            
            if(count($params) === 0) return $route;
            
            foreach($params as $param => $value){
                $route = str_replace($param, $value, $route);
            }

            return $route;

        }
        return false;
    }

    public static function setBaseUrl(String $url){
        Route::$baseUrl = $url;
    } 

    /**
     * add Middlewates
     */
    public function middleware($callback){

        if(!isset(self::$middlewares[$this->method][$this->route])) self::$middlewares[$this->method][$this->route] = [];

        array_push(self::$middlewares[$this->method][$this->route], $callback);
        return $this;
    }


    public static function load($route = "", $method = ""){
    
        if(!isset($_GET['route']) || count(Route::$routes) === 0) return;

        $route = '/'.$_GET['route'];
        $method = $_SERVER['REQUEST_METHOD'];

    
        $key = Route::getKey($route, $method);
        

        if($key && isset(Route::$routes[$method][$key])){

            $p = Route::$routes[$method][$key];
            
            unset($_GET['route']);

            if(isset(self::$middlewares[$method][$key])){

                foreach(self::$middlewares[$method][$key] as $middleware){
                    if(!call_user_func($middleware)){
                        throw new RouteHttpException(403);
                    }
                }

            }

            $class =  new $p[0]();
            $method = $p[1];
            $class->$method();

            return;
        }

        throw new RouteHttpException(404);
        
    }

    public static function getKey($r, $method){

        $REQUEST_URI = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $GETParam = explode('?', $REQUEST_URI);
        $GETParam = count($GETParam) === 2 ? $GETParam[1] : null;
        
        if($GETParam !== null) self::loadGetParam($GETParam);

        /**
         * If there arent exist params in the url (ex: route/{param}) 
         * @return $r
         */
        if(isset(Route::$routes[$method][$r])) return $r;
        


        $r = explode('/', $r);
        $rSize = count($r);  

        $routes = array_keys(Route::$routes[$method]);
     
        /**
         * Filter all urls with the same lenght 
         */
        $routes = array_filter($routes, function ($val) use ($rSize) {
            $val = explode('/', $val);
            return count($val) === $rSize ? $val : null;
        });


        $route = '';
        $matched = 0;
        $dataParam = [];

        foreach($routes as $k){
    
           $k = explode('/', $k);
        
           $match = 0;
           $dataParam = [];

           for($i=1; $i<$rSize; $i++){
                $params = false;
                if(isset($k[$i][0]) && $k[$i][0] === '{'){

                    $dataKey = substr($k[$i], 1, strlen($k[$i])-2 );
     
                    switch($method){
                        case 'POST': $_POST[$dataKey] = $r[$i]; break;
                        default: $dataParam[$dataKey] = $r[$i];;
                    }  
                  
                    $params = true;

                }else if($k[$i] === $r[$i]){
                    $match++;
                }else{
                    $match--;
                }

           }

           if($match > $matched || ($params && $match === 0 && $matched === 0)){
               $route = $k;
               $matched = $match;
               foreach($dataParam as $key => $value) $_GET[$key] = $value;
               if($matched === count($r)-1) break;
           }

        }
        
        return (!empty($route)) ? implode('/',$route) : false;
    }


    private static function loadGetParam($params){
        
        $params = parse_str($params, $outputParams);;
        
        foreach($outputParams as $key => $value){
            $_GET[$key] = $value;
        }
    }

}

class RouteHttpException extends Exception{
    
    public function __construct($code){
        parent::__construct('HTTP '.$code, $code);    
    }
}