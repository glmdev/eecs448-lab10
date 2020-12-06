<?php

namespace common\database;

class Repository {
    protected $table;
    protected $primary_key;
    protected $fields = [];

    protected $connection;

    function __construct() {
        $this->connection = new Connection();
        $this->connection->connect();
    }

    function __destruct() {
        $this->connection->close();
    }

    public function create($record) {
        $fields = [];
        $arg_parts = [];
        $args = [];

        foreach ( $this->fields as $field ) {
            if ( isset($record[$field]) ) {
                $fields[] = $field;
                $args[] = $record[$field];
                $arg_parts[] = '?';
            }
        }

        $query = 'INSERT INTO ' . $this->table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $arg_parts) . ')';
        $insert_id = $this->connection->insert($query, $args);

        $record[$this->primary_key] = $insert_id;
        return $record;
    }

    public function find_by_id($primary_key) {
        $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM ' . $this->table . ' WHERE ' . $this->primary_key . ' = ?';
        $results = $this->connection->fetch($query, [$primary_key]);

        if ( sizeof($results) > 0 ) {
            return $results[0];
        }
    }

    public function find($filter = []) {
        $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM ' . $this->table . ' WHERE ';
        list($wheres, $where_args) = $this->build_wheres_from_filter($filter);

        $query .= implode(' AND ', $wheres);
        $query .= ' ORDER BY ' . $this->primary_key . ' ASC';
        return $this->connection->fetch($query, $where_args);
    }

    public function find_one($filter = []) {
        $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM ' . $this->table . ' WHERE ';
        list($wheres, $where_args) = $this->build_wheres_from_filter($filter);

        $query .= implode(' AND ', $wheres) . ' LIMIT 1';
        $results = $this->connection->fetch($query, $where_args);

        if ( sizeof($results) > 0 ) {
            return $results[0];
        }
    }

    public function update($record) {
        $query = 'UPDATE ' . $this->table . ' SET ';
        $query_args = [];

        $sets = [];
        foreach ( $this->fields as $field ) {
            if ( isset($record[$field]) ) {
                $sets[] = $field . '=?';
                $query_args[] = $record[$field];
            }
        }

        $query = 'UPDATE ' . $this->table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $this->primary_key . ' = ?';
        $query_args[] = $record[$this->primary_key];

        $this->connection->execute($query, $query_args);
        return $record;
    }

    public function save($record) {
        if ( isset($record[$this->primary_key]) ) {
            return $this->update($record);
        } else {
            return $this->create($record);
        }
    }

    public function delete($record) {
        $primary_key = $record[$this->primary_key];

        $query = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->primary_key . ' = ?';
        $this->connection->execute($query, [$primary_key]);
    }

    protected function build_wheres_from_filter($filter = []) {
        if ( !$filter ) {
            return [['1=1'], []];
        }

        $wheres = [];
        $where_args = [];
        foreach ( $this->fields as $field ) {
            if ( isset($filter[$field]) ) {
                $val = $filter[$field];

                if ( is_string($val) || is_numeric($val) ) {
                    $wheres[] = $field . ' = ?';
                    $where_args[] = $val;
                } else if ( is_array($val) ) {
                    $where_items = [];

                    foreach ( $val as $item ) {
                        $where_items[] = '?';
                        $where_args[] = $item;
                    }

                    $wheres[] = $field . ' IN (' . implode(',', $where_items) . ')';
                }
            }
        }

        return [$wheres, $where_args];
    }
}
