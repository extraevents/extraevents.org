<?php

class db {

    CONST CONFIG = 'DB';

    protected static $_instance;
    protected static $dbs = [];
    protected static $insert_id = FALSE;
    protected static $affected_rows = FALSE;
    protected static $exec_count = [];
    protected static $configs;

    static function __autoload() {
        self::$configs = config::get(__CLASS__);

        foreach (['main', 'helper'] as $db) {
            self:: add_connection($db);
        }
    }

    static function add_connection($db_key) {
        $config = self::$configs->$db_key;
        $connection = mysqli_init();
        mysqli_real_connect($connection
                , $config->host
                , $config->username
                , $config->password
                , $config->schema
                , $config->port);
        if (mysqli_connect_errno()) {
            die("<h1>Error establishing a database {$db_key} connection</h1>");
        }
        mysqli_query($connection, "SET CHARSET UTF8");

        $db = new stdClass();
        $db->config = $config;
        $db->connection = $connection;
        self::$dbs[$db_key] = $db;
    }

    static function get_db($db_key = FALSE) {
        if ($db_key) {
            return self::$dbs[$db_key];
        } else {
            return reset(self::$dbs);
        }
    }

    static function get_db_keys() {
        return array_keys(self::$dbs);
    }

    static function escape($str) {
        return mysqli_escape_string(self::get_db()->connection, $str);
    }

    static function close() {
        db_count::save();
        foreach (self::$dbs as $db) {
            mysqli_close($db->connection);
        }
    }

    static function row($sql, $db_key = FALSE, $statements = []) {
        $result = self::exec($sql, $db_key, $statements);
        return $result->fetch_object();
    }

    static function rows($sql, $db_key = FALSE, $statements = []) {
        $result = self::exec($sql, $db_key, $statements);
        $objects = [];
        while ($object = $result->fetch_object()) {
            $objects[] = $object;
        }
        return $objects;
    }

    static function exec($sql, $db_key = FALSE, $statements = []) {
        $connection = self::get_db($db_key)->connection;
        foreach ($statements as $key => $value) {
            $value_escape = mysqli_escape_string(self::$connection, $value);
            $sql = str_replace(":$key", "'$value_escape'", $sql);
        }
        $result = mysqli_query($connection, $sql);
        if (!$result) {
            $error = "Query:<br>$sql<br>Error:<br>" . mysqli_error($connection) . "<br>";
            trigger_error($error, E_USER_ERROR);
        }
        self::$insert_id = $connection->insert_id;
        self::$affected_rows = $connection->affected_rows;
        if (!$db_key) {
            $db_key = array_key_first(self::$dbs);
        }
        self::$exec_count[$db_key] ??= 0;
        self::$exec_count[$db_key]++;
        return $result;
    }

    static function exec_count() {
        return self::$exec_count;
    }

    static function id() {
        return self::$insert_id;
    }

    static function affected() {
        return self::$affected_rows;
    }

    static function clearing_table($table, $second, $db_key) {
        db::exec("DELETE FROM $table WHERE `timestamp` < (NOW() - INTERVAL $second SECOND)",
                $db_key);
        return db::affected();
    }

    static function check_table($table, $sql_create, $db_key) {
        self::exec("SHOW TABLES like '$table'", $db_key);
        !self::affected() ? self::exec($sql_create, $db_key) : null;
    }

}
