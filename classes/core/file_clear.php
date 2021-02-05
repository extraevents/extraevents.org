<?php

class file_clear {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_log();
    }

    public static function run() {
        $config_full = config::get_full();
        foreach ($config_full as $class => $config) {
            foreach ($config->dir ?? [] as $dir) {
                $clear = $dir->clear ?? FALSE;
                if ($clear) {
                    get_class_methods($class);
                    cron::exec_task(
                            __CLASS__ . "::go",
                            $dir,
                            $clear,
                            $log_id
                    );
                }
            }
        }
    }

    public static function go($dir) {

        $dir_full_env = file::dir($dir->parent) . '/' . $dir->name . '/' . file::get_env();
        $dir_full = file::dir($dir->parent) . '/' . $dir->name;
        $depth_second = $dir->depth;
        $files = 0;
        $glob = array_merge(glob($dir_full_env . "/*"), glob($dir_full . "/*"));
        foreach ($glob as $file) {
            $filemtime = filemtime($file);
            if ($filemtime < (time() - $depth_second)) {
                $files++;
                file::unlink($file);
                db::exec(" INSERT INTO " . self::table_log() . " "
                        . "(`file`,`action`,`filemtime`) "
                        . "VALUES ('$file','delete','" . date('Y-m-d H:i:s', $filemtime) . "')",
                        helper::db());
            }
        }
        return $files;
    }

    private static function check_table_log() {
        $table = self::table_log();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `action` varchar(16) DEFAULT NULL,
                    `file` varchar(255) DEFAULT NULL,
                    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `filemtime` timestamp NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return self::$config->table->log->name;
    }

}
