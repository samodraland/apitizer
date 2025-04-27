<?php

    spl_autoload_register(function ($class) {
        $prefixes = [
            'Controller\\' => 'controller/',
            'Model\\' => 'model/',
            'Core\\' => 'core/',
            'Middleware\\' => 'middleware/',
            'Helper\\' => 'helper/',
        ];
        $file = str_replace('\\', '/', $class) . '.php';

        foreach (explode(PATH_SEPARATOR, get_include_path()) as $dir) {
            if (strpos($class, $prefix) === 0) {
                $relativeClass = substr($class, strlen($prefix));
                $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        }
    });
?>