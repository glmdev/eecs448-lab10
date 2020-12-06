<?php

class AutoLoad {
    public static function load($class_name) {
        $file = str_replace("\\", '/', $class_name) . '.php';
        $path = system_path($file);

        if ( file_exists($path) ) {
            include_system($file, false);

            if ( class_exists($class_name) ) {
                return true;
            }
        }

        return false;
    }
}

spl_autoload_register('AutoLoad::load');
