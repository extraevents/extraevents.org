<?php

class file {

    CONST DIR = '.files';

    protected static $dirs = [];
    public $path = false;
    public $dirname = false;
    public $basename = false;
    public $timestamp = false;
    public $exists = false;

    static function __autoload() {
        $dirs = config::get(__CLASS__);
        foreach ($dirs as $db => $dir) {
            self::$dirs[$db] = $dir;
        }
    }

    public static function dirs() {
        return array_keys(self::$dirs);
    }

    public static function dir($parent) {
        return self::$dirs[$parent];
    }

    public static function get_env() {
        return str_replace('www.', '', strtolower(filter_input(INPUT_SERVER, 'SERVER_NAME')));
    }

    public static function build_path($dirs) {
        $path = false;
        foreach ($dirs as $dir) {
            $path = $path ? $path . '/' . $dir : $dir;
            !file_exists($path) ? mkdir($path) : null;
        }
        return $path;
    }

    static function check($file) {
        if (!file_exists($file)) {
            trigger_error("[$file] not exists", E_USER_ERROR);
        }
        if (is_file($file)) {
            return file_get_contents($file);
        }
    }

    static function unlink($file) {
        if (file_exists($file) and!is_dir($file)) {
            return unlink($file);
        }
        return false;
    }

    static function rand() {
        return bin2hex(random_bytes(16));
    }

    public function __construct($path) {
        $this->path = $path;
        $path_parts = pathinfo($path);
        $this->dirname = $path_parts['dirname']??false;
        $this->basename = $path_parts['basename'];
        if (file_exists($path)) {
            $this->exists = true;
            $this->timestamp = date("Y-m-d\TH:i:s\Z", filectime($path));
        }
    }



    public function get_size() {
        return
                round(filesize($this->path) / 1024, 1);
    }

}
