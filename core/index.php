<?php

namespace Core;
use Core\Properties;
use Helper\Utils;

class Index extends Data{

    function __construct(){
        ob_start("ob_gzhandler");
        mb_internal_encoding("UTF-8");
        date_default_timezone_set( Properties::getProperties("app", "timezone") );
        
        $parsedurl = parse_url($_SERVER['REQUEST_URI']);
        parse_str($parsedurl["query"], $parsed);
        $this::setData("queries", $parsed);
        $url = rtrim($parsedurl["path"], "/");
        $base = dirname($_SERVER['PHP_SELF']);
        $endpoint = (Properties::getProperties("app", "supportHtaccess")) ? substr($url, strlen($base) + 1) : $_GET["endpoint"];
        $controllfile = explode("/",$endpoint);

        $this::setData("urls.controller", ( !empty($endpoint) ) ? $controllfile[0] : $controllfile);
        $this::setData("urls.endpoint", "/".$endpoint);
        $this::setData("urls.base", $base);
        $this::setData("urls.method", $_SERVER['REQUEST_METHOD']);
        $this::setData("urls.url", (isset($_SERVER["HTTPS"]) ? "https" : "http") . "://".$_SERVER["HTTP_HOST"]. $url);
        $this::setData("urls.path", $base . "/" . $endpoint);
        $this::setData("headers", Utils::getHeaders());

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: *");
            header("Content-Encoding: gzip", false);
            header("Access-Control-Max-Age: 86400");
            header("Access-Control-Allow-Credentials: true");
        }

        if ($this::getData("urls.method") == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization");
            exit(0);
        }
    }

    public function run(){
        $className = "Controller\\".ucfirst(trim($this::getData("urls.controller")));

        if (class_exists($className)){
            $inputs = file_get_contents("php://input");
            $class = new $className;

            if (method_exists($class,strtolower($this::getData("urls.method")))){

                if ($this::getData("urls.method") == "PUT" || $this::getData("urls.method") == "PATCH"){
                    if ($inputs != ""){
                        $boundary = substr($inputs, 0, strpos($inputs, "\r\n"));
                        $parts = array_slice(explode($boundary, $inputs), 1);
                        $postvars = [];
                        foreach ($parts as $part) {
                            list($rawheaders, $body) = explode("\r\n\r\n", $part, 2);
                            $rawheaders = explode("\r\n", $rawheaders);
                            $headers = [];
                            foreach ($rawheaders as $header) {
                                list($headername, $headervalue) = explode(": ", $header);
                                $headers[$headername] = $headervalue;
                            }
                            if (isset($headers['Content-Disposition'])) {
                                preg_match('/name="([^"]+)"/', $headers['Content-Disposition'], $matches);
                                $name = $matches[1];
                                $postvars[$name] = $body;
                            }
                        }
                    }else{
                        die(Utils::response(400));
                    }
                }else{
                    if($inputs != "" && $this::getData("urls.method") != "GET"){
                        $postvars = json_decode($inputs,true);
                    }else{
                        if ($this::getData("urls.method") == "GET"){
                            $postvars = [];
                        }else{
                            parse_str($inputs,$postvars);
                        }                            
                    }                        
                }
                $this::setData("data", array_merge($_POST, $postvars, $_FILES));
                call_user_func( [$class,strtolower($this::getData("urls.method"))] );
            }else{
                die(Utils::response(405));
            }
        }else{
            die(Utils::response(404));
        }            
    }        
}

?>