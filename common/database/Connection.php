<?php

namespace common\database;

class Connection {
    protected static $mysqli;

    protected $config;

    function __construct() {
        $this->config = config('database');
    }

    public function connect() {
        if ( !static::$mysqli ) {
            static::$mysqli = new \mysqli(
                $this->config['url'],
                $this->config['username'],
                $this->config['password'],
                $this->config['database']
            );
        }

        if ( static::$mysqli->connect_errno ) {
            throw new \Exception('Unable to connect to database: ' . static::$mysqli->connect_error);
        }
    }

    public function escape($value) {
        return static::$mysqli->real_escape_string($value);
    }

    public function execute($query, $args = [], $returns_statement = false) {
        $statement = static::$mysqli->prepare($query);

        if ( sizeof($args) > 0 ) {
            $types = '';
            foreach ( $args as $arg ) {
                if ( is_int($arg) ) {
                    $types .= 'i';
                } else if ( is_float($arg) || is_double($arg) ) {
                    $types .= 'd';
                } else if ( is_string($arg) ) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }

            $statement->bind_param($types, ...$args);
        }

        $statement->execute();

        if ( $returns_statement ) return $statement;
        return $statement->get_result();
    }

    public function insert($query, $args = []) {
        $statement = $this->execute($query, $args, true);
        return $statement->insert_id;
    }

    public function fetch($query, $args = []) {
        $result = $this->execute($query, $args);

        $rows = [];
        while ( $row = $result->fetch_assoc() ) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function close() {
        if ( static::$mysqli ) {
            static::$mysqli->close();
            static::$mysqli = null;
        }
    }
}
