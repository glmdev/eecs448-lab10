<?php

function config($path, $default_value = null) {
    $parts = explode('.', $path);

    if ( !$parts[0] ) {
        return $default_value;
    }

    $config = require_system('config/' . $parts[0] . '.php');
    foreach ( $parts as $i => $part ) {
        if ( $i !== 0 && $config ) {
            $config = $config[$part];
        }
    }

    return $config;
}
