<?php

class page {

    static $file;
    static $is_post = false;
    static $title = false;
    static $objects = [];
    public static $section = false;
    public static $navigations = false;
    public static $parrent_url = false;

    CONST DIR = 'pages';
    CONST ACTIONS_DIR = 'actions';

    static function __autoload() {
        $config = config::get(__CLASS__);
        $pages = yml::get($config->yml);
        $request = request::get(0);
        if (!$request) {
            $request = $pages->default;
        }
        $page = $pages->$request ?? false;
        if (!$page or $request == 'default' or $request == 'navigations') {
            page_404();
        }

        self::$file = $page->file;
        self::$title = t($page->title ?? false);
        self::$section = $page->section ?? false;
        self::$navigations = $pages->navigations;

        if (filter_input(INPUT_SERVER, 'REQUEST_METHOD') == 'POST') {
            self::$is_post = true;
            form::run();
        }
    }

    static function get_object($name) {
        return
                self::$objects[$name] ?? null;
    }

    static function is_post() {
        return self::$is_post;
    }

    static function set_title($title) {
        self::$title = $title;
    }

    static function set_parrent_url($parrent_url) {
        self::$parrent_url = $parrent_url;
    }

    static function post_required() {
        if (!self::is_post()) {
            form::not_post();
        }
    }

    static function add_object($name, $object) {
        self::$objects[$name] = $object;
    }

    public static function include_main() {
        $file = self::$file;
        $dir = dirname(glob(self::DIR . "/*/$file.php")[0]);
        grand::set_grand($dir);
        include("$dir/$file.php");
    }

    public static function action($file) {
        foreach (glob(self::DIR . '/*/' . self::ACTIONS_DIR . "/$file.php")as $file_find) {
            include $file_find;
        }
    }

    public static function include($file, $data = null) {

        $dir = dirname(glob(self::DIR . "/*/$file.php")[0]);
        $extensions = ['sql', 'php', 'css', 't.php', 'js'];
        $tags = ['css' => 'style', 'js' => 'script'];
        foreach ($extensions as $extension) {
            $filename = "$dir/$file.$extension";
            if (!file_exists($filename)) {
                continue;
            }
            if ($extension == 'sql') {
                $sql = file_get_contents($filename);
                continue;
            }
            $tag_name = $tags[$extension] ?? false;
            echo $tag_name ? "<$tag_name>" : '';
            include $filename;
            echo $tag_name ? "</$tag_name>" : '';
        }
    }

    static function get_index() {

        $http_host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $php_self = filter_input(INPUT_SERVER, 'PHP_SELF');

        $index = str_replace('index.php', '', $php_self);

        if (substr($index, -1) == '/') {
            $index = substr_replace($index, '', -1);
        }

        if (substr($index, -1) == '/') {
            $index = substr_replace($index, '', -1);
        }

        return
                "//$http_host" . $index;
    }

    static function push() {

        $title = self::$title;
        $title .= $title ? ' | ' : '';
        $title .= config::get()->title;
        $index = page::get_index();

        return
                str_replace(
                ['%title', '%i/'],
                [$title, $index . '/'],
                ob_get_clean());
    }

}
