<?php

namespace Core;
use Core\Middleware;
use Helper\Utils;

abstract class Controller extends Middleware {

    private static $result = [];

    final protected function map($arrayRoute): void{
        foreach ($arrayRoute as $route => $callback) {
            $paramKeys = [];
            $pattern = preg_replace_callback("/:([a-zA-Z_][a-zA-Z0-9_]*)/", function ($matches) use (&$paramKeys) {
                $paramKeys[] = $matches[1];
                return "([A-Za-z0-9._-]+)";
            }, $route);

            $fullPattern = "#^" . $this->getData("urls.base") . "/" . $this->getData("urls.controller");
            $pattern = trim($pattern, "/");
            if ($pattern !== "") {
                $fullPattern .= "/" . $pattern;
            }
            $fullPattern .= "$#iu";
            if (preg_match($fullPattern, $this->getData("urls.path"), $matches)) {
                $this->setData("urls.route", $route);
                array_shift($matches);                
                foreach ($paramKeys as $i => $key) {
                    $this->setData("keys.$key", $matches[$i]);
                }

                
                if (is_callable($callback)) {
                    $this->executeMiddlewares("before");
                    $result = call_user_func(function() use ($callback) {
                        $result = call_user_func($callback, Data::class);
                        return $result;
                    });
                    echo $result;
                    return;
                }
            }
        }

        die(Utils::response(404));
    }

    final protected function json( $result ): string{
        $isJSONP = array_key_exists( "jsonpcallback", $this->getData("queries") );
        $contentType = ( !$isJSONP ) ? "application/json" : "application/javascript";
        header("Content-Type: $contentType; charset=UTF-8");
        if ( $isJSONP ) $jsonpCallback = $this->getData("queries.jsonpcallback");
        
        $finalResult = $this->modifyResult($result);
        header( Utils::responseHeader( $finalResult["response"] ) );
        return ( !$isJSONP ) ? json_encode($finalResult) : $jsonpCallback."(".json_encode($finalResult).")";
    }

    final protected function html( $template, $result = null ): string{
        header("Content-type: text/html; charset=UTF-8");
        $finalResult = $this->modifyResult($result);
        return Utils::renderTemplate( $template, $finalResult );
    }

    final protected function xml( $result ): string{
        $finalResult = $this->modifyResult($result);
        header("Content-type: text/xml;charset=UTF-8");
        header( Utils::responseHeader( $finalResult["response"] ) );
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root/>');
        $this->parseXML( $finalResult, $xml);
        return $xml->asXML();			
    }

    private function parseXML( $data, &$xml_data ): void {
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

    private function modifyResult($result): array{
        $this->setData("records", $result);
        $this->executeMiddlewares("after");
        return $this->getData("records");
    }
}

?>