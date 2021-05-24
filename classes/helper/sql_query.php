<?php

class sql_query {

    CONST DIR = 'sql_queries/';

    static function row($key, $values = [], $db_key = FALSE) {
        $query = self::get_query($key, $values);
        return db::row($query, $db_key);
    }

    static function exec($key, $values = [], $db_key = FALSE) {
        $query = self::get_query($key, $values);
        return db::exec($query, $db_key);
    }

    static function execs($key, $values = [], $db_key = FALSE) {
        $queries = self::get_query($key, $values);
        foreach (explode(';', $queries) as $query) {
            db::exec($query, $db_key);
        }
        return;
    }

    static function rows($key, $values = [], $db_key = FALSE) {
        $query = self::get_query($key, $values);
        return db::rows($query, $db_key);
    }

    static private function get_query($key, $values) {
        $sql_file = self::DIR . $key . '.sql';
        if (!file_exists($sql_file)) {
            user_error("sql [$key] not found", E_USER_ERROR);
        }
        $query = file_get_contents($sql_file);
        foreach ($values as $k => $v) {
            $query = str_replace("@:$k:", $v, $query);
        }

        preg_match('/@:(.*?):/', $query, $match);
        if ($match[1] ?? false) {
            user_error("[{$match[1]}] not defined for sql query [$key]", E_USER_ERROR);
        }
        return $query;
    }

}
