<?php

    spl_autoload_register(function ($class) {
        $prefixes = [
            "Controller\\" => "src/controller/",
            "Helper\\" => "src/helper/",
            "Middleware\\" => "src/middleware/",
            "Model\\" => "src/model/",
            "Core\\" => "core/",
        ];
        $file = str_replace("\\", "/", $class) . ".php";

        foreach ($prefixes as $prefix => $baseDir) {
            if (strpos($class, $prefix) === 0) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace("\\", "/", $relativeClass) . ".php";
    
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    });
?>