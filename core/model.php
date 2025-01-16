<?php

    abstract class Model extends PDO{
        private $dbstr;
        private $result = array();
        private $statement = null;
        
        private function connectionString($dbdriver, $dbhost, $dbname){
          $dblist = array(
            "mysql" => "mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4",
            "postgresql" => "pgsql:host=$dbhost;port=5432;dbname=$dbname",
            "sqlserver" => "sqlsrv:Server=$dbhost,1433;Database=$dbname;ConnectionPooling=0"
          );
          return $dblist[$dbdriver];
        }

        function __construct(){
          $dbstr = Properties::getProperties("db");
          try{
            $opt = array(
                  parent::ATTR_ERRMODE => parent::ERRMODE_EXCEPTION,
                  parent::ATTR_EMULATE_PREPARES => 1,
                  parent::ATTR_PERSISTENT => 1,
                  parent::MYSQL_ATTR_USE_BUFFERED_QUERY => 0,
                  parent::ATTR_DEFAULT_FETCH_MODE => parent::FETCH_ASSOC
              );
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
            $errors = array(
              "message" => "Server error"
            ); 
            if (Properties::getProperties("showerror")){
              $errors = array(
                "code" => $e->getCode(),
                "message" => $e->getMessage()
              );
            }
            die(Utils::response(500, $errors) );
          }
        }

        protected function execute( $sql , $params = array() ){
            if ( trim($sql) != "" ){
              $query = preg_replace("/\s+/", " ", trim($sql));
              $querytype = strtolower(substr($query,0,6));
              try{
                  $collection = array();
                  $this->statement = parent::prepare( $query );
                  if (is_array($params[0])){
                    foreach($params as $key => $value){
                      do{
                        $collection += $this->statement->fetchAll();
                      } while ($this->statement->nextRowset());
                    }
                  }else{
                    $this->statement->execute( $params );
                      do{
                        $collection += $this->statement->fetchAll();
                      } while ($this->statement->nextRowset());
                  }                  
                  
                  $this->result = array(
                    "response" => 200,
                    "message" => Utils::responseHeader(200, true),
                    "result" => $collection
                  );

              }catch(PDOException $e){        
                $errors = array(
                  "message" => "Server error"
                ); 
                if (Properties::getProperties("showerror")){
                  $errors = array(
                    "code" => $e->getCode(),
                    "message" => $e->getMessage()
                  );
                }
                die(Utils::response(500, $errors));
              }
              return $this->result;
            }else{
              die(Utils::response(500, array(
                "message" => "Server error"
              )));
            }
        }
    }
?>
