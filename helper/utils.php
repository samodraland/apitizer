<?php
    class Utils{

        private static $alphanumeric = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        
        public static function responseHeader($code, $textonly = false){
            include("static/responsecode.php");
            $protocol = (isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.0");            
            return (!$textonly) ? $protocol ." ". $http[ $code ] : substr($http[ $code ], 4);
        }

        public static function response( $code, $msg = [] ){
            header( self::responseHeader($code) );
            header("Content-Type: application/json; charset=UTF-8");
            return json_encode(array(
                "response" => $code,
                "message" => self::responseHeader($code, true),
                "result" => $msg
            ));
        }

        public static function generateRandomString($length = 6) {
            return substr(str_shuffle(str_repeat($x=self::$alphanumeric, ceil($length/strlen($x)) )),1,$length);
        }

        public static function generatePassword($length = 8, $pwd = null){
            $str = (is_null($pwd)) ? self::generateRandomString($length) : $pwd;
            return password_hash($str, PASSWORD_BCRYPT, ["cost" => 12]);
        }

        public static function constructSchema(...$param){
            $model = array();
            $merged = array_merge(...$param);
            foreach($merged as $key => $value ){
                $model[":".strtolower($key)] = self::validateString($value);
            }
            return $model;
        }

        public static function validateString($str){
            return stripslashes(strip_tags(str_replace("'","",$str)));
        }

        public static function validateXmlString($str){
            return htmlspecialchars(utf8_encode(stripslashes(preg_replace("/[^\x20-\x7E]/","",str_replace("\n","<br/>",$str)))),ENT_QUOTES);
        }

        public static function formatDate($date,$format){
            setlocale(LC_TIME, Properties::getProperties("app","lctime"));
            $time = strtotime($date);
            return date($format,$time);
        }
        
        public static function getHeaders() {
            $headers = array();
            $allowedHeader = Properties::getOtherProperties("allowed","headers");
            $keys = array_keys(array_filter($_SERVER, function($value, $key) use ($allowedHeader) {
                return count(array_intersect($allowedHeader, explode('_', $key))) > 0;
            }, ARRAY_FILTER_USE_BOTH));
            $headers = array_intersect_key($_SERVER, array_flip($keys));

            return $headers;
        }
        
        public static function getCurrentDir(){
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            return $protocol.$domainName.dirname($_SERVER['PHP_SELF']);
        }
        
        public static function renderTemplate( $template , $data = null ){
            $view = trim(Properties::getProperties("view"),"/");
            $url = trim(Properties::getProperties("url"),"/");
            if (file_exists("$view/$template.php")){
                $postdata = (!is_null($data)) ? http_build_query($data) : "";
                $ch = curl_init("$url/$view/$template.php");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                $output = curl_exec($ch);
                curl_close($ch);
                return $output;
            }else{
                return self::response(404, "Template file doesn't exist");
            }
        }
                        
        public static function testQuery($sql, $params = array()){
            foreach($params as $key => $value) $sql = str_replace($key, "'$value'", $sql);
            return $sql;
        }
    }
?>
