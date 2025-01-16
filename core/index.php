<?php

    class Index{

        private $method = "";
        private $collection = array();
        private $token = null;

        function __construct(){
            ob_start('ob_gzhandler');
            date_default_timezone_set( Properties::getProperties("app", "timezone") );
            
            $this->method = $_SERVER['REQUEST_METHOD'];
            $parsedurl = parse_url($_SERVER['REQUEST_URI']);
            parse_str($parsedurl["query"], $this->collection["queries"]);
            $url = rtrim($parsedurl["path"], "/");
            $base = dirname($_SERVER['PHP_SELF']);
            $endpoint = substr($url, strlen($base) + 1);
            $controllfile = explode("/",$endpoint);
            
            $this->collection["url"]["controller"] = ( !empty($endpoint) ) ? $controllfile[0] : $controllfile;
            $this->collection["url"]["endpoint"] = "/".$endpoint;
            $this->collection["url"]["base"] = $base;
            $this->collection["url"]["method"] = $this->method;
            $this->collection["url"]["url"] = (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://".$_SERVER["HTTP_HOST"]. $url;
            $this->collection["url"]["path"] = $base . "/" . $endpoint;
            $this->collection["headers"] = Utils::getHeaders();
            $this->collection["keys"] = array();
            $this->token = new Token();
            if (isset($_SERVER['HTTP_ORIGIN'])) {
                header("Access-Control-Allow-Origin: *");
                header("Content-Encoding: gzip", false);
                header("Access-Control-Max-Age: 86400");
                header("Access-Control-Allow-Credentials: true");
            }
            if ($this->method == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
                exit(0);
            }
        }

        public function run($home = null){
            if (trim($this->collection["url"]["endpoint"]) == "/" && !is_null($home)){
                $this->collection["url"]["controller"] = $home;
            }
            if (file_exists("controller/".$this->collection["url"]["controller"].".php")){
                parse_str(file_get_contents("php://input"),$postvars);
                $this->collection["data"] = array_merge($_POST, $postvars, $_FILES);
                $controller = ucfirst($this->collection["url"]["controller"]);
                $class = new $controller;
                $class->setParameters( $this->collection );
                if (method_exists($class,strtolower($this->method))){
                    call_user_func(
                        array($class,strtolower($this->method))
                    );
                }else{
                    die(Utils::response(405));
                }
            }else{
                die(Utils::response(404));
            }            
        }        
    }

?>