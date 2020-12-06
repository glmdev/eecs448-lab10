<?php

function system_root() {
    $root = dirname(dirname(__FILE__));
    return $root;
}

function system_path($path) {
    if ( $path[0] === '/' ) {
        $path = substr($path, 1);
    }

    return implode('/', [system_root(), $path]);
}

function require_system($path, $once = true) {
    if ( $once ) {
        return require_once system_path($path);
    } else {
        return require system_path($path);
    }
}

function include_system($path, $once = true) {
    if ( $once ) {
        return include_once system_path($path);
    } else {
        return include system_path($path);
    }
}

function system_url($path) {
    if ( $path[0] === '/' ) {
        $path = substr($path, 1);
    }

    return implode('/', [SYSTEM_URL, $path]);
}

function system_redirect($path) {
    header('Location: ' . system_url($path));
    exit;
}

function no_direct_access() {
    $key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
    $caller_file = debug_backtrace()[$key]['file'];

    if ( $caller_file === $_SERVER['SCRIPT_FILENAME'] ) {
        header("HTTP/1.1 401 Unauthorized");
        echo 'Forbidden';
        exit;
    }
}
