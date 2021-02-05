<?php

class wcaapi {

    public static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function get($path) {
        $url = self::$config->url . '/' . $path;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        self::log($path, $status);
        if ($status != 400 and $status != 404) {
            return
                    json_decode($response);
        }
        return
                null;
    }

    private static function log($path, $status) {
        $path_escape = db::escape($path);
        db::exec("INSERT INTO `" . self::table_log() . "` "
                . " (`path`,`status`) "
                . " VALUES "
                . " ('$path_escape','$status') ",
                helper::db());
    }

        static function table_log() {
        return
                self::$config->table->log->name;
    }
    
    static function __recreater() {
        $table = self::table_log();
        db::exec(" DROP TABLE IF EXISTS `$table`",
                helper::db());
        db::exec(" CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `path` varchar(255) NOT NULL,
                    `status` varchar(11) DEFAULT NULL,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

}
