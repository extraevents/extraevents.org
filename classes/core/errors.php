<?php

class errors {

    CONST DIR1 = '.files_tmp';
    CONST DIR2 = 'errors';

    private static $echo;

    static function __autoload() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        set_error_handler("errors::handler");
        self::$echo = config::isTest();
        register_shutdown_function("errors::shutdown");
    }

    static function handler($errno, $errstr, $errfile, $errline) {
        ob_end_clean();
        $errorCodes = [
            E_ERROR => 'Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'CoreError',
            E_CORE_WARNING => 'CoreWarning',
            E_COMPILE_ERROR => 'CompileError',
            E_COMPILE_WARNING => 'CompileWarning',
            E_USER_ERROR => 'UserError',
            E_USER_WARNING => 'UserWarning',
            E_USER_NOTICE => 'UserNotice',
            E_DEPRECATED => 'Depricated',
            E_USER_DEPRECATED => 'UserDepricated'
        ];

        $time = date("Y-m-d H:i:s");
        if (isset($errorCodes[$errno])) {
            $errcode = $errorCodes[$errno];
        } else {
            $errcode = $errno;
        }
        $message = "$errcode at $time in $errfile ($errline)";

        $backtrace = debug_backtrace();

        foreach ($backtrace as $key => $value) {
            if (isset($value['function'])
                    and $value['function'] == 'trigger_error') {
                unset($backtrace[$key]);
            }
            if (isset($value['class'])
                    and $value['class'] == __CLASS__) {
                unset($backtrace[$key]);
            }
        }

        $backtrace = array_reverse($backtrace);
        $cash = md5($errno . $errstr . $errfile . $errline);
        $dir = self::dir();
        $file = __DIR__ . "/../../$dir/$cash.err";

        $debug = [];
        $debug['SERVER'] = $_SERVER;
        $debug['POST'] = $_POST;
        $debug['SESSION'] = $_SESSION;
        $debug['FILES'] = $_FILES;
        $debug['debug_backtrace'] = $backtrace;
        $text = "
$message
<br>
<pre>
$errstr
" . print_r($debug, true) . "
</pre>";

        if (!file_exists($file)) {
            $handle = fopen($file, "w");
            fwrite($handle, $text);
            fclose($handle);
        }

        if (self::$echo) {
            page_500("#$cash", $text);
        } elseif (in_array($errno,
                        [
                            E_ERROR,
                            E_USER_ERROR,
                            E_CORE_ERROR,
                            E_COMPILE_ERROR
                ])) {
            page_500("#$cash");
        }
    }

    static function shutdown() {
        $err = error_get_last();
        if (is_null($err)) {
            return;
        }
        switch ($err['type']) {
            case E_DEPRECATED:
                $type = E_USER_DEPRECATED;
                break;
            case E_NOTICE:
                $type = E_USER_NOTICE;
                break;
            case E_WARNING:
                $type = E_USER_WARNING;
                break;
            default:
                $type = E_USER_ERROR;
        }

        if (!in_array($err['type'], [E_WARNING, E_NOTICE])) {
            trigger_error(
                    '[shutdown] ' . print_r($err, true), $type
            );
        }
    }

    private static function dir() {
        $server = str_replace('www.', '', strtolower(filter_input(INPUT_SERVER, 'SERVER_NAME')));
        $dirs = [
            self::DIR1,
            self::DIR2,
            $server
        ];
        $path = '';
        foreach ($dirs as $dir) {
            $path = $path ? $path . '/' . $dir : $dir;
            if (!is_dir($path)) {
                mkdir($path);
            }
        }
        return $path;
    }

    static function get() {
        $result = [];
        $dir = self::dir();
        foreach (scandir($dir) as $file) {
            $result[] = [
                'file' => $file,
                'time' => filectime("$dir/$file")
            ];
        }
        return($result);
    }

    static function done($id) {
        self::setStatus($id, self::_DONE);
    }

    static function work($id) {
        self::setStatus($id, self::_WORK);
    }

    static function skip($id) {
        for ($i = 1; $i <= $id; $i++) {
            $status = self::getStatus($i);
            if (in_array($status, [self::_NEW, self::_WORK])) {
                self::setStatus($i, self::_SKIP);
            }
        }
    }

    private static function getStatus($id) {
        if (!$id) {
            return;
        }
        $dir = self::dir();
        foreach (scandir($dir) as $file) {
            $explode = explode("_", $file);
            if (sizeof($explode) == 4
                    and $explode[0] == $id) {
                return $explode[3];
            }
        }
    }

    private static function setStatus($id, $status) {
        if (!$id) {
            return;
        }
        $dir = self::dir();
        foreach (scandir($dir) as $file) {
            $explode = explode("_", $file);
            if (sizeof($explode) == 4
                    and $explode[0] == $id) {
                $explode[3] = $status;
                $newFile = implode("_", $explode);
                rename($dir . "/" . $file
                        , $dir . "/" . $newFile);
            }
        }
    }

}
