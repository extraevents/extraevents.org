<?php

class cash {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function get($process) {
        $table_log = self::$config->table->log->name;
        return
                db::row("SELECT cash FROM cash WHERE process='$process'",
                        'helper')->cash ?? null;
    }

    static function set($process, $cash) {
        db::exec("INSERT INTO cash (process, cash)
                    VALUES ('$process','$cash')
                    ON DUPLICATE KEY UPDATE cash = '$cash' ",
                'helper');
    }

}
