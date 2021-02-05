<?php

class smtp {

    protected static $config;

    static function __autoload() {
        self::$config = config::get(__CLASS__);
        self::check_table_log();
    }

    static function put($to, $subject, $message) {
        $to_escape = db::escape($to);
        $subject_escape = db::escape($subject);
        $message_escape = db::escape($message);
        db::exec(" INSERT INTO `" . self::table_log() . "`"
                . " (`to`,`subject`,`body`) "
                . " VALUES ('$to_escape','$subject_escape','$message_escape')",
                helper::db());

        return db::id();
    }

    static function send() {
        $mails = db::rows("SELECT `id`, `to`,`subject`,`body` "
                        . " FROM `" . self::table_log() . "`"
                        . " WHERE result is null "
                        . " LIMIT " . self::$config->send_max,
                        helper::db());

        foreach ($mails as $mail) {
            $contentMail = self::getContentMail($mail->subject, $mail->body);
            $result = self::_send($mail->to, $contentMail);
            $result_escape = db::escape($result);
            db::exec("UPDATE `" . self::table_log() . "`"
                    . " SET result = '$result_escape'"
                    . " WHERE id = $mail->id",
                    helper::db());
        }
        return $mails;
    }

    private static function _send($to, $contentMail) {
        $host = self::$config->host;
        $port = self::$config->port;
        $username = self::$config->username;
        $password = self::$config->password;

        if (!$socket = fsockopen($host, $port, $errorNumber, $errorDescription, 30)) {
            return "$errorNumber:$errorDescription";
        }
        if (!self::_parseServer($socket, "220")) {
            return 'Connection error';
        }

        $server_name = $_SERVER["SERVER_NAME"];
        fputs($socket, "EHLO $server_name\r\n");
        if (!self::_parseServer($socket, "250")) {
            // если сервер не ответил на EHLO, то отправляем HELO
            fputs($socket, "HELO $server_name\r\n");
            if (!self::_parseServer($socket, "250")) {
                fclose($socket);
                return 'Error of command sending: HELO';
            }
        }

        fputs($socket, "AUTH LOGIN\r\n");
        if (!self::_parseServer($socket, "334")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, base64_encode($username) . "\r\n");
        if (!self::_parseServer($socket, "334")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, base64_encode($password) . "\r\n");
        if (!self::_parseServer($socket, "235")) {
            fclose($socket);
            return 'Autorization error';
        }

        fputs($socket, "MAIL FROM: <{$username}>\r\n");
        if (!self::_parseServer($socket, "250")) {
            fclose($socket);
            return 'Error of command sending: MAIL FROM';
        }

        $emails_to_array = explode(',', str_replace(" ", "", $to));
        foreach ($emails_to_array as $email) {
            fputs($socket, "RCPT TO: <{$email}>\r\n");
            if (!self::_parseServer($socket, "250")) {
                fclose($socket);
                return 'Error of command sending: RCPT TO';
            }
        }

        fputs($socket, "DATA\r\n");
        if (!self::_parseServer($socket, "354")) {
            fclose($socket);
            return 'Error of command sending: DATA';
        }

        fputs($socket, "$contentMail\r\n.\r\n");
        if (!self::_parseServer($socket, "250")) {
            fclose($socket);
            return 'E-mail didn\'t sent';
        }

        fputs($socket, "QUIT\r\n");
        fclose($socket);
        return true;
    }

    private static function _parseServer($socket, $response) {
        $responseServer = 'xxx';
        while (substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;
    }

    private static function getContentMail($subject, $message) {
        $from = self::$config->from;
        $username = self::$config->username;

        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "=?=\r\n";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";
        $headers .= "From: $from <$username>\r\n";
        $contentMail .= "$headers\r\n";
        $contentMail .= "$message\r\n";
        return $contentMail;
    }

    private static function check_table_log() {
        $table = self::table_log();
        db::check_table($table,
                " CREATE TABLE `$table` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `to` varchar(255) DEFAULT NULL,
                    `subject` varchar(255) DEFAULT NULL,
                    `body` text DEFAULT NULL,
                    `result` text,
                    `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                    `smtp` timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;",
                helper::db());
    }

    private static function table_log() {
        return self::$config->table->log->name;
    }

}
