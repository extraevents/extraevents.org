<?php

class wca {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    public static function db() {
        return self::$config->db;
    }

}
