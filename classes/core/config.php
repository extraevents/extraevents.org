<?php

class config {

    private const DIR = '.config_key';
    private const DIR_DEFAULT = '.config';
    private const SERVER_TEST = 'localhost';

    protected static $server;
    protected static $config;

    static function __autoload() {
        $server = str_replace('www.', '', strtolower(filter_input(INPUT_SERVER, 'SERVER_NAME')));
        self::$server = $server;
        if (self::isTest() and!file_exists(self::DIR . "/.test_key")) {
            self::error("wrong test key");
        }
        if (!self::isTest() and!file_exists(self::DIR . "/.prod_key")) {
            self::error("wrong prod key");
        }
        $config_default_content = file_get_contents(self::DIR_DEFAULT . '/default.yml');
        $config_const = yml::get(self::DIR_DEFAULT . '/const.yml');
        $config_key = yml::get(self::DIR . '/' . $server . '.yml');
        $config_content = $config_default_content;
        
        foreach ($config_key as $key => $value) {
            $config_content = str_replace("#$key", $value, $config_content);
        }
        
        foreach ($config_const as $key => $value) {
            $config_content = str_replace("#$key", $value, $config_content);
        }
        
        self::$config = yml::build($config_content);
    }

    static function get($class = __CLASS__) {
        $value = self::$config->$class ?? FALSE;
        if (!$value) {
            self::error("config [$class] not found");
        }
        return $value;
    }

    static function get_full() {
        return self::$config;
    }

    static function isTest() {
        return
                self::$server == self::SERVER_TEST;
    }

    static private function error($error) {
        trigger_error($error, E_USER_ERROR);
    }

}
