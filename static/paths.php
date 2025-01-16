<?php
    $modelLocation = "model/";
    $controllerLocation = "controller/";
    $coreLocation = "core/";
    $helperLocation = "helper/";
    
    set_include_path ( 
        $coreLocation . PATH_SEPARATOR . 
        $controllerLocation . PATH_SEPARATOR . 
        $modelLocation . PATH_SEPARATOR . 
        $helperLocation
    );
    spl_autoload_register();
?>