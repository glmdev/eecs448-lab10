<?php

namespace common;

class Request {
    protected $_get = [];
    protected $_post = [];

    public static function capture() {
        $req = new static();
        $req->_get = $_GET;
        $req->_post = $_POST;
        return $req;
    }

    public function input($path = null) {
        if ( !$path ) {
            return array_merge($this->_get, $this->_post);
        }

        $path_parts = explode('.', $path);

        $get_value = $this->_get;
        foreach ( $path_parts as $part ) {
            if ( $get_value ) {
                $get_value = $get_value[$part];
            }
        }

        $post_value = $this->_post;
        foreach ( $path_parts as $part ) {
            if ( $post_value ) {
                $post_value = $post_value[$part];
            }
        }

        if ( $get_value ) return $get_value;
        if ( $post_value ) return $post_value;
    }
}
