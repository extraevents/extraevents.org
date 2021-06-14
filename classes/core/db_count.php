<?php

class db_count {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function save() {
        $table_log = self::$config->table->log->name;
        $db_count = json_encode(db::exec_count());
        $request = db::escape(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        $is_post = page::is_post() + 0;
        db::exec(" INSERT INTO `$table_log` (`db_count`,`request`,`is_post`) VALUES ('$db_count','$request',$is_post)",
                helper::db());
    }

}
