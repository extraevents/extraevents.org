<?php

class backup extends Ifsnop\Mysqldump\Mysqldump {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_log();
    }

    static function sql($attributes) {
        $db_key = $attributes->db;
        $dir_key = $attributes->dir;
        db::add_connection($db_key);

        return self::use_mysqldump_sql(
                        $db_key, $dir_key
        );
    }

    static function tsv($attributes) {
        $db_key = $attributes->db;
        $dir_key = $attributes->dir;
        db::add_connection($db_key);

        $path = self::build_path($dir_key);
        $filename_template = $db_key . "_" . date(self::$config->format_time);
        $filename_zip_path = "$path/$filename_template.tsv.zip";

        $zip = new ZipArchive();
        $zip->open($filename_zip_path, ZIPARCHIVE::CREATE);
        $filenames = [];
        $TABLE_SCHEMA = db::get_db($db_key)->config->schema;
        $tables_rows = db::rows("Select * from information_schema.TABLES where TABLE_SCHEMA='$TABLE_SCHEMA'",
                        $db_key);
        foreach ($tables_rows as $table) {
            $TABLE_NAME = $table->TABLE_NAME;
            $filename = "$path/$TABLE_NAME.tsv";
            $filenames[$TABLE_NAME] = $filename;
            $f = fopen($filename, 'w');
            $columns = [];
            $columns_rows = db::rows("Select * from information_schema.COLUMNS where TABLE_SCHEMA='$TABLE_SCHEMA' and TABLE_NAME='$TABLE_NAME' order by ORDINAL_POSITION",
                            $db_key);
            foreach ($columns_rows as $column) {
                $columns[] = $column->COLUMN_NAME;
            }

            fwrite($f, implode("\t", $columns) . "\n");
            $rows = db::rows("Select * from $TABLE_NAME",
                            $db_key);
            foreach ($rows as $row) {
                fwrite($f, str_replace(array("\r\n", "\r", "\n"), "<br>", implode("\t", (array) $row)) . "\n");
            }
            fclose($f);

            $zip->addFile($filename, "$TABLE_NAME.tsv");
        }

        $zip->close();
        foreach ($filenames as $filename) {
            unlink($filename);
        }

        self::log($filename_zip_path, $db_key, $dir_key, 'tsv');
        return $filename_template;
    }

    static private function use_mysqldump_sql($db_key, $dir_key) {
        $path = self::build_path($dir_key);
        $filename_template = $db_key . "_" . date(self::$config->format_time);
        $filename_path = "$path/$filename_template.sql";
        $filename = "$filename_template.sql";
        $filename_zip_path = "$filename_path.zip";
        self::mysqldump_init(
                db::get_db($db_key)->config,
                ['add-drop-table' => true]
        )->start($filename_path);
        $zip = new ZipArchive();
        $zip->open($filename_zip_path, ZIPARCHIVE::CREATE);
        $zip->addFile($filename_path, $filename);
        $zip->close();
        unlink($filename_path);
        self::log($filename_zip_path, $db_key, $dir_key, 'sql');
        return $filename_template;
    }

    static private function build_path($dir_key) {
        return file::build_path(
                        [
                            file::dir(self::$config->dir->$dir_key->parent),
                            self::$config->dir->$dir_key->name,
                            file::get_env()
                        ]
        );
    }

    static private function mysqldump_init($config, $dumpSettings) {
        return new self(
                "mysql:host={$config->host};port={$config->port};dbname={$config->schema}",
                $config->username,
                $config->password,
                $dumpSettings);
    }

    private static function log($path, $db_key, $dir_key, $format) {
        $size = filesize($path);
        db::exec(" INSERT INTO `" . self::table_log() . "`"
                . " (`path`,`db`,`dir`,`format`,`size`) "
                . " VALUES ('$path','$db_key','$dir_key','$format',$size)",
                helper::db());
    }

    public static function last($db_key, $dir_key, $format) {
        return new file(db::row(" SELECT path FROM `" . self::table_log() . "`"
                        . " WHERE `db` = '$db_key' "
                        . " AND `dir` = '$dir_key' "
                        . " AND `format` = '$format' "
                        . " ORDER BY id DESC LIMIT 1 ",
                        helper::db())->path ?? FALSE);
    }

    private static function check_table_log() {
        $table = self::table_log();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `path` varchar(255) DEFAULT NULL,
                    `db` varchar(255) DEFAULT NULL,
                    `dir` varchar(255) DEFAULT NULL,
                    `format` varchar(255) DEFAULT NULL,
                    `size` int(11),
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return self::$config->table->log->name;
    }

}
