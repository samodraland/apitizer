<?php

$properties = array(
    "app" => array(
        "lctime" => "id_ID",
        "timezone" => "Asia/Jakarta",
        "supportHtaccess" => true,
        "connectionheader" => "auto" //close | Keep-Alive | auto
    ),
    "env" => array(
        "dev" => "http://localhost/apitizer",
        "staging" => "https://yourstagingdomain.com",
        "prod" => "https://yourproductiondomain.com"
    ),    
    "db" => array(
        "dev" => array(
            "driver" => "mysql",
            "host" => "localhost",
            "name" => "apitizer",
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
    "email" => array(
        "dev" => array(
            "host" => "localhost",
            "displayname" => "Apitizer Boilerplate",
            "replyto" => "apitizer@localhost.com",
            "username" => "apitizer@localhost.com",
            "email" => "apitizer@localhost.com",
            "password" => "apitizer",
            "embedimage" => "/view/email-template"
        ),
        "staging" => array(
            "host" => "yourdomain",
            "displayname" => "Your display name",
            "replyto" => "youraccount@yourdomain.com",
            "username" => "yourusername@yourdomain.com",
            "email" => "yourusername@yourdomain.com",
            "password" => "yourpassword",
            "embedimage" => "/your/image/path"
        ),
        "prod" => array(
            "host" => "yourdomain",
            "displayname" => "Your display name",
            "replyto" => "youraccount@yourdomain.com",
            "username" => "yourusername@yourdomain.com",
            "email" => "yourusername@yourdomain.com",
            "password" => "yourpassword",
            "embedimage" => "/your/image/path"
        )
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