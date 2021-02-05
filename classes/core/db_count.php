<?php

class db_count {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_log();
    }

    static function save() {
        $db_count = json_encode(db::exec_count());
        $request = db::escape(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        $is_post = page::is_post() + 0;
        db::exec(" INSERT INTO `" . self::table_log() . "` (`db_count`,`request`,`is_post`) VALUES ('$db_count','$request',$is_post)",
                helper::db());
    }

    private static function check_table_log() {
        $table = self::table_log();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `db_count` varchar(255) NULL,
                    `request` varchar(255) NULL,
                    `is_post` bit NULL,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return self::$config->table->log->name;
    }

}
