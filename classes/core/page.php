<?php

class page {

    static $section = false;
    static $config;
    static $navigations = false;
    static $file = false;
    static $title = false;
    static $post = false;
    static $objects = [];
    static $grand = false;
    static $grand_action = false;
    static $parrent_url = false;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        $pages = yml::get(self::$config->yml);
        $type = request::get(0);
        if (!$type) {
            $type = $pages->default;
        }

        $page = $pages->$type ?? FALSE;
        if ($page) {
            self::$section = $page->section ?? FALSE;
            self::$file = $page->file;
            self::$title = t($page->title ?? FALSE);
        } else {
            page_404();
        }
        self::$navigations = $pages->navigations;
        self::$post = (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == "POST");
        if (self::$post) {
            form::run();
        }
    }

    static function get_file() {
        return self::$file;
    }

    static function is_post() {
        return self::$post;
    }

    static function post_required() {
        if (!self::is_post()) {
            form::not_post();
        }
    }

    static function get_section() {
        return self::$section;
    }

    static function set_title($title) {
        self::$title = $title;
    }

    static function get_title() {
        $title = self::$title;
        return
                ($title ? "$title | " : '') . config::get()->title;
    }

    static function get_navigations() {
        return self::$navigations;
    }

    static function add_object($name, $object) {
        self::$objects[$name] = $object;
    }

    static function get_object($name) {
        return self::$objects[$name] ?? null;
    }

    static function set_parrent_url($parrent_url) {
        self::$parrent_url = $parrent_url;
    }

    static function get_parrent_url() {
        return
                self::$parrent_url;
    }

    public static function include_main() {
        $file = self::get_file();
        $dir = self::dir($file);
        grand::set_grand($dir);
        include("$dir/$file.php");
    }

    public static function action($file) {
        foreach (glob("pages/*/actions/$file.php")as $file_find) {
            include $file_find;
        }
    }

    public static function include($file, $data = null, $ext = []) {
        $dir = self::dir($file);
        $files = [
            '.sql' => 'sql',
            '.php' => '',
            '.css' => 'style',
            '.t.php' => '',
            '.js' => 'script'
        ];
        foreach ($files as $k => $v) {
            $filename = "$dir/$file$k";
            if (file_exists($filename)) {
                if ($v == 'sql') {
                    $sql = file_get_contents($filename);
                    continue;
                }

                echo "<!-- + $filename -->";
                if ($v) {
                    echo "<$v>";
                    include $filename;
                    echo "</$v>";
                } else {
                    include $filename;
                }
            } else {
                echo "<!-- - $filename -->";
            }
        }
    }

    private static function dir($file) {
        foreach (glob("pages/*/$file.php")as $file_find) {
            return dirname($file_find);
        }
    }

}
