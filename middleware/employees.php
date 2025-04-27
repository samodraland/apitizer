<?php

namespace Middleware;
use Core\Middleware;

class Employees extends Middleware{
    
    public function get($sequence){
        if($sequence == "before"){
            $this::setData("flag.employeebefore", "employee before");
        }else{
            $this::setData("flag.employeeafter", "employee after");
            $this::setData("records.message", "employee middleware injected on after");
            $this::setData("records.result.0.new", "new injected");
        }
    }

    public function post($sequence){
        if ($sequence == "before"){
            $this::setData("flag.first", "first name edited");
            $this::setData("flag.last", "last name edited");
            $this::setData("flag.email", "email edited");
        }else{
            $this::setData("result.response", 404);
        }
    }
}

?>