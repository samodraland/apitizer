<?php

$properties = array(
    "app" => array(
        "name" => "project name",
        "lctime" => "id_ID",
        "timezone" => "Asia/Jakarta",
        "dev" => array(
            "url" => "http://localhost:8080"
        ),
        "prod" => array(
            "url" => "https://domain.com"
        ),
    ),
    "url" => array(
        "dev" => "http://localhost/api/myproject",
        "prod" => "https://domain.com/api/myproject"
    ),
    "email" => array(
        "dev" => array(
            "host" => "localhost",
            "displayname" => "Company name",
            "replyto" => "company@localhost.com",
            "useragent" => "Company",
            "username" => "user.no-reply@localhost.com",
            "email" => "company.email@localhost.com",
            "password" => "mypassword",
            "embedimage" => "view/img/"
        ),
        "prod" => array(
            "host" => "domain.com",
            "displayname" => "Company name",
            "replyto" => "company.no-reply@domain.com",
            "useragent" => "Company",
            "username" => "user.no-reply@domain.com",
            "email" => "company.no-reply@domain.com",
            "password" => "secret",
            "embedimage" => "view/img/"
        )
    ),
    "db" => array(
        "dev" => array(
            "driver" => "mysql", //mysql || postgresql || sqlserver
            "host" => "localhost",
            "name" => "db_name",
            "user" => "root",
            "pwd" => ""
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
        "dev" => "view",
        "prod" => "view"
    ),
    "assets" => array(
        "dev" => "assets",
        "prod" => "assets"
    ),
    "showerror" => array(
        "dev" => true,
        "prod" => false
    )
);
?>