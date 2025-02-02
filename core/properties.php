<?php

    class Properties{
        
        private static $prop = array();

        private static function init($filename){
            if (!array_key_exists($filename, self::$prop)){
                $content = include ("static/$filename.php");
                self::$prop[$filename] = $content;
            }
        }

        public static function getProperties($key, $subkey = null){
            self::init("properties");
            $host = parse_url($_SERVER["HTTP_HOST"])["path"];
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $env = array_search(rtrim($protocol.$host,"/"), self::$prop["properties"]["env"]);
            return ( is_null($subkey) ) ? self::$prop["properties"][$key][$env] : self::$prop["properties"][$key][$subkey];            
        }

        public static function getOtherProperties($filename, $key, $subkey = null){
            self::init($filename);
            return ( is_null($subkey) ) ? self::$prop[$filename][$key] : self::$prop[$filename][$key][$subkey];
        }
    }

?>