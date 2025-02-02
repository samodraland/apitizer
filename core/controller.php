<?php

    abstract class Controller {

        private $parameters = array();
        private $resetParam = array();

        public function setParameters( $arraydata ){
            $this->parameters = $arraydata;
            $this->resetParam = $arraydata;
        }

        protected function map( $arrayRoute ){
            foreach($arrayRoute as $endpoint => $callback){
                if ( $endpoint == "/" && ( $this->parameters["url"]["controller"] == ltrim($this->parameters["url"]["endpoint"],"/") ) ){
                    if ( is_callable( $callback ) ) echo call_user_func( $callback, $this->parameters );
                    return;
                }else{
                    if ( preg_match_all("/([:])\w+/", trim($endpoint), $match) ){
                        $this->parameters = $this->resetParam;
                        foreach($match[0] as $key => $value){
                            $endpoint = preg_replace( "/$value/i","([A-Za-z0-9._-]+)",$endpoint );
                            $this->parameters["keys"][ltrim($value,":")] = "";
                        }
                    }

                    $pattern = "#^(".$this->parameters["url"]["base"]."/".$this->parameters["url"]["controller"].")$endpoint$#iu";
                    
                    if ( preg_match($pattern, $this->parameters["url"]["path"], $matches) ){
                        echo $this->execMatch( $matches, $callback );
                        return;
                    }
                }
            }

            die(Utils::response(404));
        }

        private function execMatch( $matches, $callback ){
            unset( $matches[0],$matches[1] );
            $matches = array_values($matches);
            $idx = 0;
            foreach($this->parameters["keys"] as $key => $value){
                $this->parameters["keys"][$key] = $matches[$idx];
                $idx++;
            }

            if ( is_callable( $callback ) ) return call_user_func( $callback, $this->parameters );

            $this->parameters = $this->resetParam;
        }

        protected function json( $result ){
            $isJSONP = array_key_exists( "jsonpcallback", $this->parameters["queries"] );
            $contentType = ( !$isJSONP ) ? "application/json" : "application/javascript";
            header("Content-Type: $contentType; charset=UTF-8");
            if ( $isJSONP ) $jsonpCallback = $this->parameters["queries"]["jsonpcallback"];
            
            header( Utils::responseHeader( $result["response"] ) );
            return ( !$isJSONP ) ? json_encode($result) : $jsonpCallback."(".json_encode($result).")";
        }

        protected function html( $template, $result = null ){
            header("Content-type: text/html; charset=UTF-8");
            return Utils::renderTemplate( $template, $result );
        }

        protected function xml( $result ){
            header("Content-type: text/xml;charset=UTF-8");
            $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root/>');
            $this->parseXML( $result, $xml);
            return $xml->asXML();			
        }

        private function parseXML( $data, &$xml_data ) {
            foreach( $data as $key => $value ) {
                if( is_array($value) ) {
                    if( is_numeric($key) ) $key = "record";
                    $subnode = $xml_data->addChild($key);
                    $this->parseXML($value, $subnode);
                } else {
                    $xml_data->addChild($key, Utils::validateXmlString($value));
                }
             }
        }
    }

?>