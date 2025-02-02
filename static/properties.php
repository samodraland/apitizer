<?php

$properties = array(
    "app" => array(
        "lctime" => "id_ID",
        "timezone" => "Asia/Jakarta",
        "supportHtaccess" => true
    ),
    "env" => array(
        "dev" => "http://localhost",
        "staging" => "https://yourstagingdomain.com",
        "prod" => "https://yourproductiondomain.com"
    ),    
    "db" => array(
        "dev" => array(
            "driver" => "mysql", //mysql || postgresql || sqlserver
            "host" => "localhost",
            "name" => "db_name",
            "user" => "root",
            "pwd" => ""
        ),
        "staging" => array(
            "driver" => "mysql", //mysql || postgresql || sqlserver
            "host" => "localhost",
            "name" => "your_db_name",
            "user" => "your_db_username",
            "pwd" => "your_db_password"
        ),
        "prod" => array(
            "driver" => "mysql", //mysql || postgresql || sqlserver
            "host" => "localhost",
            "name" => "db_name",
            "user" => "root",
            "pwd" => "secret"
        ),    
    ),
    "view" => array(
        "dev" => "/view",
        "staging" => "/view",
        "prod" => "/view"
    ),
    "assets" => array(
        "dev" => "/assets",
        "staging" => "/assets",
        "prod" => "/assets"
    ),
    "showerror" => array(
        "dev" => true,
        "staging" => false,
        "prod" => false
    )
);

return $properties;
?>