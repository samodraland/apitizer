<?php
    class Utils{

        private static $alphanumeric = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        
        public static function responseHeader($code, $textonly = false){
            include("static/responsecode.php");
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');            
            return (!$textonly) ? $protocol .' '. $http[ $code ] : substr($http[ $code ], 4);
        }

        public static function hasValue( $val ){
            return ( isset($val) && !is_null($val) && !empty($val) ) ? true : false;
        }

        public static function response( $code, $msg = [] ){
            header( "Access-Control-Allow-Origin: *", false );
            header( "Content-Type: application/json" );
            header( self::responseHeader($code) );
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
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }
        
        public static function getCurrentDir(){
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
            $domainName = $_SERVER['HTTP_HOST'];
            return $protocol.$domainName.dirname($_SERVER['PHP_SELF']);
        }
        
        public static function templating( $template , $data = null ){
            $view = Properties::getProperties("view");
            $url = Properties::getProperties("url");
            if (file_exists("$view$template.php")){
                $postdata = (!is_null($data)) ? http_build_query($data) : "";
                /*
                $opts = array('http' =>
                    array(
                        'method'  => 'POST',
                        'header'  => 'Content-Type: application/x-www-form-urlencoded',
                        'content' => $postdata
                    )
                );
                $context = stream_context_create($opts);
                $output = file_get_contents( "$url/$view$template.php", false, $context);
                */
                $ch = curl_init("$url/$view$template.php");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
                $output = curl_exec($ch);
                curl_close($ch);
                return $output;
            }else{
                return self::response(404, "Template file doesn't exist");
            }
        }
        
        public static function base64ToImage($dataUri, $filename, $realpath, $thumbnailpath = ""){
            if ($dataUri != ""){
                $extension = explode("/",explode(";",$dataUri)[0]);
                $realfile = $realpath.$filename.".".$extension[1];
                $img = base64_decode(explode(",", $dataUri )[1]);
                file_put_contents($realfile, $img);
                if ($thumbnailpath != ""){
                    $thumbnail = $thumbnailpath.$filename.".".$extension[1];
                    copy( $realfile , $thumbnail );
                    $image = new Image();
                    $image->load($thumbnail);
                    $image->scale(15);
                    $image->save($thumbnail);    
                }
                return $filename.".".$extension[1];
            }else{
                return "";
            }            
        }
        
        public static function testQuery($sql, $params = array()){
            foreach($params as $key => $value) $sql = str_replace($key, "'$value'", $sql);
            return $sql;
        }
    }
?>
