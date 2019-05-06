<?php

class Request {

    public static $url_path_to_script, $requested_path, $requested_path_array, $requested_clean_path, $requested_clean_path_array;

    public static function init() {
        $path_to_script = str_replace(basename($_SERVER["SCRIPT_NAME"]), '', $_SERVER["SCRIPT_NAME"]);
        if (substr($path_to_script, 0, 1) == '/') {
            $path_to_script = substr($path_to_script, 1);
        }
        self::$url_path_to_script = str_replace('xpm/library/xtreme_core/', '', $path_to_script);
        preg_match('/\/*(.*)\/*/', $_SERVER['REQUEST_URI'], $match);
        if (strstr($match[1], '?')) {
            $match[1] = preg_replace('/\?.*/', '', $match[1]);
        }
        self::$requested_path = $match[1];
        self::$requested_path_array = array_filter(explode('/', Request::$requested_path), 'strlen');
        self::$requested_clean_path = str_replace(self::$url_path_to_script, '', self::$requested_path);
        self::$requested_clean_path_array = array_filter(explode('/', Request::$requested_clean_path), 'strlen');
    }

}
