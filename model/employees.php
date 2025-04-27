<?php

namespace Model;
use Core\Model;

class Employees extends Model{

    private $query = "
        select
            id,
            first_name as FirstName,
            last_name as LastName,
            email as Email,
            status as Status
        from
            employee
        %s
        order by
            first_name asc
        %s
    ";

    public function getAllEmployees(){
        $sql = sprintf($this->query, "", "");
        return $this->execute( $sql );
    }
    
    public function getEmployeeById( $param ){
        $sql = sprintf($this->query, "where id = :id", "");
        return $this->execute( $sql, $param );
    }

    public function getByStatus( $param, $page ){
        $str = "limit 10 offset %d";
        $limit = sprintf($str, $page);
        $where = "where status = :status";
        $sql = sprintf($this->query, $where, $limit);
        return $this->execute( $sql, $param );
    }

    public function searchEmployee( $param ){
        $sql = sprintf($this->query, "where (first_name like concat('%',:keyword,'%') or last_name like concat('%',:keyword,'%') or email like concat('%',:keyword,'%')) and status = :status", "");
        return $this->execute( $sql, $param );
    }   

    public function newEmployee( $param ){
        $sql = "insert into employee(first_name, last_name, email) values(:first, :last, :email)";
        return $this->execute( $sql, $param );
    }

    public function updateEmployee( $param ){
        $sql = "update employee set first_name = :first, last_name = :last, email = :email where id = :id";
        return $this->execute( $sql, $param );
    }

    public function deleteEmployee( $param ){
        $sql = "delete from employee where id = :id";
        return $this->execute($sql, $param);
    }
}

?>