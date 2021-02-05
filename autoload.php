<?php

session_start();

include 'vendor/autoload.php';
spl_autoload_register(function ($class) {
    foreach (glob("classes/*/$class.php") as $file) {
        require_once $file;
        if (method_exists($class, '__autoload')) {
            $class::__autoload();
        }
    }
});
new errors();
new template();

foreach (['functions', 'functions_scramble'] as $dir) {
    $objects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir),
            RecursiveIteratorIterator::SELF_FIRST);
    foreach ($objects as $name => $object) {
        if (strpos($name, '.php') !== false) {
            require_once $name;
        }
    }
}