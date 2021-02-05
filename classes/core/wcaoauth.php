<?php

class wcaoauth {

    protected static $config;
    protected static $user = null;

    CONST SESSION = 'wcaoauth.session';

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function url() {
        return
                self::$config->url . '/authorize'
                . '?'
                . "client_id=" . self::$config->client_id . "&"
                . "redirect_uri=" . urlencode(self::$config->url_refer) . "&"
                . "response_type=code&"
                . "scope=" . self::$config->scope . "";
    }

    static function authorize() {

        $error = filter_input(INPUT_GET, 'error');
        self::set_session();

        if ($error == 'access_denied') {
            self::session_error(['error' => $error]);
            message::set("wcaoauth.$error!");
            self::location();
        }

        $code = filter_input(INPUT_GET, 'code');
        if (!$code) {
            return null;
        }

        $accessToken = self::getAccessTokenCurl($code);
        if (!$accessToken) {
            self::location();
        }

        $getMeCurl = self::getMeCurl($accessToken);
        if (!$getMeCurl) {
            self::session_error(['id' => false]);
            self::location();
        } else {
            self::session_begin($getMeCurl);
        }
        return $getMeCurl;
    }

    private static function buildQueryForAccessToken($code) {
        return http_build_query(
                [
                    'grant_type' => 'authorization_code',
                    'client_id' => self::$config->client_id,
                    'client_secret' => self::$config->client_secret,
                    'code' => $code,
                    'redirect_uri' => self::$config->url_refer
        ]);
    }

    private static function getAccessTokenCurl($code) {
        $postdata = self::buildQueryForAccessToken($code);
        $ch = curl_init(self::$config->url . '/token');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded', '"Accept-Language: en-us,en;q=0.5";']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if (json_decode($result)->error ?? FALSE) {
            trigger_error(__CLASS__ . ".getToken: $result <br>" . print_r($postdata, true), E_USER_ERROR);
        }

        if ($status != 200) {
            trigger_error(__CLASS__ . ".getToken: $status<br>$url", E_USER_ERROR);
        }

        return json_decode($result)->access_token;
    }

    private static function getMeCurl($accessToken) {
        $ch = curl_init(wcaapi::$config->url . "/me");
        curl_setopt($ch, CURLOPT_HTTPHEADER,
                ['Content-Type: application/json',
                    "Authorization: Bearer $accessToken"
                ]
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        if ($status != 200) {
            trigger_error(__CLASS__ . ".getMe: $status<br>$url", E_USER_ERROR);
        }

        if (isset(json_decode($result)->me->id)) {
            $me = json_decode($result)->me;
            return $me;
        } else {
            return false;
        }
    }

    private static function session_error($error) {
        $session = self::get_session();

        db::exec(" INSERT INTO `" . self::table_log() . "`"
                . " (`session`)"
                . " VALUES"
                . " ('$session')",
                helper::db());
        self::session_end($error);
    }

    private static function session_begin($details) {

        $user_id = $details->id ?? FALSE;
        $wca_id = $details->wca_id ?? FALSE;
        $name = db::escape($details->name) ?? FALSE;
        $session = self::get_session();

        db::exec(" INSERT INTO `" . self::table_log() . "` "
                . " (`session`, `user_id`, `wca_id`,`name`) "
                . " VALUES"
                . " ('$session', '$user_id','$wca_id','$name')",
                helper::db());
        return db::id();
    }

    public static function session_end() {
        $session = self::get_session();
        db::exec(" UPDATE `" . self::table_log() . "` "
                . " SET `auth_end`= CURRENT_TIMESTAMP "
                . " WHERE session = '" . $session . "' and `auth_end` is null ",
                helper::db());
    }

    public static function session_end_all() {
        $user_id = self::user_id();
        if (!$user_id) {
            return false;
        }
        db::exec(" UPDATE `" . self::table_log() . "` "
                . " SET `auth_end`= CURRENT_TIMESTAMP "
                . " WHERE user_id = '" . $user_id . "' and `auth_end` is null ",
                helper::db());
    }

    static function user_id() {
        return self::get_user()->user_id ?? FALSE;
    }

    static function wca_id() {
        return self::get_user()->wca_id ?? FALSE;
    }

    static function name() {
        return self::get_user()->name ?? FALSE;
    }

    static function get_user() {
        if (self::$user) {
            $row = self::$user;
        } else {
            $session = self::get_session();
            $row = db::row("SELECT user_id, wca_id, name "
                            . "FROM `" . self::table_log() . "` "
                            . "WHERE session = '" . $session . "' and `auth_end` is null ",
                            helper::db());
            if (!$row) {
                unset($_SESSION[self::SESSION]);
            }
            self::$user = $row;
        }
        return $row;
    }

    private static function get_session() {
        return $_SESSION[self::SESSION] ??= FALSE;
    }

    private static function set_session() {
        $session = bin2hex(random_bytes(16));
        $_SESSION[self::SESSION] = $session;
        return $session;
    }

    public static function get_count_session() {
        $user_id = self::user_id();
        if (!$user_id) {
            return false;
        }
        return count(
                db::rows("SELECT session "
                        . "FROM `" . self::table_log() . "` "
                        . "WHERE user_id = '" . $user_id . "' and `auth_end` is null ",
                        helper::db()
                )
        );
    }

    static function table_log() {
        return
                self::$config->table->log->name;
    }

    static function __recreater() {
        $table = self::table_log();
        db::exec(" DROP TABLE IF EXISTS `$table`",
                helper::db());
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `session` varchar(52) NULL,
                    `wca_id` varchar(10) NULL,
                    `user_id` int(11) NULL,
                    `name` varchar(50) NULL,
                    `auth_begin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `auth_end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE  (`session`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

}
