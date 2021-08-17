<?php

class cash {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function get($process) {
        $table_log = self::$config->table->log->name;
        return
                db::row("SELECT cash FROM $table_log WHERE process='$process'",
                        'helper')->cash ?? null;
    }

    static function set($process, $cash) {
        $table_log = self::$config->table->log->name;
        db::exec("INSERT INTO $table_log (process, cash)
                    VALUES ('$process','$cash')
                    ON DUPLICATE KEY UPDATE cash = '$cash' ",
                'helper');
    }

}
