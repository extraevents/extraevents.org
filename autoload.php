<?php

class autoload {

    CONST VENDOR_DIR = 'vendor';
    CONST CLASSES_DIR = 'classes';
    CONST FUNCTIONS_DIR_PREFIX = 'functions';

    function __construct() {
        $this->vendor();
        $this->classes();
        $this->functions();
        new errors();
        new template();
    }

    private function vendor() {
        include_once self::VENDOR_DIR . '/autoload.php';
    }

    private function classes() {
        spl_autoload_register(function ($class_name) {
            foreach (glob(self::CLASSES_DIR . "/*/$class_name.php") as $file_name) {
                require_once $file_name;
                if (method_exists($class_name, '__autoload')) {
                    $class_name::__autoload();
                }
            }
        });
    }

    private function functions() {
        foreach (glob(self::FUNCTIONS_DIR_PREFIX . "*/*/*.php") as $file_name) {
            require_once $file_name;
        }
    }

}
