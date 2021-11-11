<?php

class discort {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
    }

    static function send($channel,$text) {
        $webhookurl = self::$config->webhookurl->$channel ?? false;
        if (!$webhookurl) {
            self::log($webhookurl, $text, 'webhookurl not found');
            return false;
        }

        $result = self::curl($webhookurl, $text);
        self::log($webhookurl, $text, $result);
        return $result == "";
    }

    static private function curl($webhookurl, $text) {

        $json_data = json_encode([
            "content" => $text,
            "tts" => false
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


        $ch = curl_init($webhookurl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    static private function log($webhookurl, $text, $result) {
        $table_log = self::$config->table->log->name;
        $text_escape = db::escape($text);
        $webhookurl_md5 = md5($webhookurl);
        db::exec("INSERT INTO $table_log (webhookurl,text, result)
                    VALUES ('$webhookurl_md5','$text_escape','$result')",
                'helper');
    }

}
