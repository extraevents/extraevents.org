<?php

class file_size {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_size();
    }

    static function save() {
        $result = [];
        foreach (file::dirs() as $dir) {
            $result[$dir] = self::save_dir(file::dir($dir));
        }
        return $result;
    }

    static function save_dir($dir) {
        $result = 0;
        foreach (scandir($dir) as $subdir) {
            if (substr($subdir, 0, 1) == '.') {
                continue;
            }
            $size = 0;
            $count = 0;
            foreach (new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator("$dir/$subdir")
            ) as $file) {
                if (!is_dir($file) and!strpos($file, '.DS_Store')) {
                    $size += $file->getSize();
                    $count++;
                }
            }
            $file_mb = ceil($size / 1024 / 1024);
            db::exec(" INSERT INTO " . self::table_size() . " "
                    . "(`dir`,`subdir`,`file_mb`,`files`) "
                    . "VALUES ('$dir','$subdir','$file_mb','$count')",
                    helper::db());
            $result += $file_mb;
        }
        return $result;
    }

    private static function check_table_size() {
        $table = self::table_size();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `dir` varchar(32) DEFAULT NULL,
                    `subdir` varchar(32) DEFAULT NULL,
                    `file_mb` int(11),
                    `files` int(11),
                    `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_size() {
        return self::$config->table->size->name;
    }

}
