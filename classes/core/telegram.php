<?php

class telegram {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function send($reciever, $subject, $text) {
        $chat_id = self::$config->chat_id->$reciever ?? false;
        if (!is_numeric($chat_id))
            return;
        if ($text != $subject) {
            $text = "$subject / $text";
        }
        $token = self::$config->token;
        if (!$chat_id) {
            self::log($chat_id, $reciever, $text, 'chat_id not found');
            return false;
        }
        if (!$token) {
            self::log($chat_id, $reciever, $text, 'token not found');
            return false;
        }

        $result = self::curl($token, $chat_id, $text);
        self::log($chat_id, $reciever, $text, $result);
        return json_decode($result)->ok ?? false;
    }

    static private function curl($token, $chat_id, $text) {
        $ch = curl_init();

        $ch = curl_init('https://api.telegram.org/bot' . $token . '/sendMessage?chat_id=' . $chat_id . '&text=' . $text);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    static private function log($chat_id, $reciever, $text, $result) {
        $table_log = self::$config->table->log->name;

        db::exec("INSERT INTO $table_log (chat_id, reciever, text, result)
                    VALUES ('$chat_id','$reciever','$text','$result')",
                'helper');
    }

}
