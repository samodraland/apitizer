<?php

  namespace Core;
  use Helper\Utils;
  use Core\Properties;
  
  abstract class Model extends \PDO{
      private $dbstr;
      private $result = [];
      private $statement = null;
      
      private function connectionString($dbdriver, $dbhost, $dbname){
        $dblist = [
          "mysql" => "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4",
          "postgresql" => "pgsql:host=$dbhost;port=5432;dbname=$dbname",
          "sqlserver" => "sqlsrv:Server=$dbhost,1433;Database=$dbname;ConnectionPooling=0"
        ];
        return $dblist[$dbdriver] ?? null;
      }

      function __construct(){
        $dbstr = Properties::getProperties("db");
        try{
          $opt = [
                parent::ATTR_ERRMODE => parent::ERRMODE_EXCEPTION,
                parent::ATTR_EMULATE_PREPARES => 1,
                parent::ATTR_PERSISTENT => 1,
                parent::ATTR_DEFAULT_FETCH_MODE => parent::FETCH_ASSOC
            ];
            parent::__construct(
              $this->connectionString(
                $dbstr["driver"],
                $dbstr["host"],
                $dbstr["name"]
              ),
              $dbstr["user"], 
              $dbstr["pwd"],
              $opt
            );
        }catch(PDOException $e){
          header( Utils::responseHeader(500) );
          $errors = [
            "message" => "Server error"
          ];
          if (Properties::getProperties("showerror")){
            $errors = [
              "code" => $e->getCode(),
              "message" => $e->getMessage()
            ];
          }
          die(Utils::response(500, $errors) );
        }
      }

      final protected function execute( $sql , $params = [] ){
        if ( trim($sql) != "" ){
          $query = preg_replace("/\s+/", " ", trim($sql));
          $querytype = strtolower(substr($query,0,6));
          try{
              $collection = [];
              $this->statement = parent::prepare( $query );
              if (is_array($params[0])){ // expecting if $params is nested array to indicates multiple parameters
                foreach ($params as $key => $value) {
                  $this->statement->execute($value);
                  $collection = array_merge($collection, $this->statement->fetchAll());
                }
              }else{
                $this->statement->execute($params);
                $collection = $this->statement->fetchAll();
              }                  
              
              $this->result = [
                "response" => 200,
                "message" => Utils::responseHeader(200, true),
                "result" => $collection
              ];
              return $this->result;
          }catch(PDOException $e){        
            $errors = [
              "message" => "Server error"
            ]; 
            if (Properties::getProperties("showerror")){
              $errors = [
                "code" => $e->getCode(),
                "message" => $e->getMessage()
              ];
            }
            die(Utils::response(500, $errors));
          }            
        }else{
          die(Utils::response(500, [
            "message" => "Server error"
          ]));
        }
      }
  }
?>
