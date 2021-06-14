<?php

class form {

    protected static $config;
    private static $form;
    private static $request = false;
    private static $action = false;

    CONST POST = 'form.post';

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_log();
        self::check_table_process();
    }

    public static function run() {
        $request_escape = db::escape(filter_input(INPUT_SERVER, 'REQUEST_URI'));
        $get_escape = db::escape(json_encode($_GET));
        $post_escape = db::escape(json_encode($_POST));
        $sesson_escape = db::escape(json_encode($_SESSION));
        $server_escape = db::escape(json_encode($_SERVER));
        $user_id = wcaoauth::user_id() + 0;

        db::exec(" INSERT INTO `" . self::table_log() . "` "
                . " (`user_id`, `request`, `get`, `post`, `session`, `server`) "
                . " VALUES"
                . " ('$user_id', '$request_escape','$get_escape',"
                . "'$post_escape','$sesson_escape','$server_escape')",
                helper::db());

        self::$form = db::id();
        self::$request = $request_escape;
        self::$action = filter_input(INPUT_POST, 'action');
        $page = filter_input(INPUT_SERVER, 'HTTP_REFERER');
        $_SESSION[self::POST][$page][self::$action] = $_POST;
    }

    public static function get($action, $name) {
        $page = self::get_url();
        $value = $_SESSION[self::POST][$page][$action][$name] ?? false;
        unset($_SESSION[self::POST][$page][$action][$name]);
        return
                $value;
    }

    public static function required() {
        $args = new stdClass();
        foreach (func_get_args() as $variable) {
            if (!isset($_POST[$variable])) {
                self::process(false,
                        ['require' => $variable],
                        'form.errors.require!');
                self::return();
            } else {
                if (is_array($_POST[$variable])) {
                    $args->$variable = $_POST[$variable];
                } else {
                    $args->$variable = trim(filter_input(INPUT_POST, $variable));
                }
            }
        }
        return
                $args;
    }

    public static function value($key) {
        return
                filter_input(INPUT_POST, $key);
    }

    public static function action() {
        return self::$action;
    }

    public static function not_post() {
        message::set('form.errors.not_post!');
        self::return('');
    }

    public static function process($status, $details, $message = false, $object = false) {
        $request = self::$request;
        $status_escape = db::escape($status);
        $details_escape = db::escape(json_encode($details));
        $message_escape = db::escape($message);
        $user_id = wcaoauth::user_id() + 0;
        $form_id = self::$form;
        db::exec(" INSERT INTO `" . self::table_process() . "` 
                    (`form_id`, `user_id`, `request`, `status`, `details`, `message`) 
                    VALUES 
                    ('$form_id','$user_id', '$request', '$status_escape','$details_escape','$message_escape')",
                helper::db());
        message::set($message);
        if ($status == TRUE) {
            unset($_SESSION[self::POST][self::get_url()]);
        }
    }

    private static function get_url() {
        return ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function return($path = false) {
        if ($path !== false) {
            header('Location: ' . page::get_index() . '/' . $path);
        } else {
            header('Location: ' . filter_input(INPUT_SERVER, 'HTTP_REFERER'));
        }
        exit();
    }

    public static function get_id() {
        return self::$form;
    }

    private static function check_table_log() {
        $table = self::table_log();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) NULL,
                    `request` text,
                    `get` text,
                    `post` text,
                    `session` text,
                    `server` text,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function check_table_process() {
        $table = self::table_process();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `form_id` int(11) NULL,
                    `user_id` int(11) NULL,
                    `request` varchar(255) DEFAULT NULL,
                    `status` varchar(255) DEFAULT NULL,
                    `details` text,
                    `message` varchar(255),
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return
                self::$config->table->log->name;
    }

    private static function table_process() {
        return
                self::$config->table->process->name;
    }

}
