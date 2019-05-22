<?php

class Xcache_engine_file {

    public static $cache_path = null;

    public static function get($key, $age) {
        self::ensure_cache_folder();
        $key = @strval($key);
        $big_filepath_json = Xcache_engine_file::$cache_path . md5($key) . '.json';
        $File_json = File::instance($big_filepath_json);
        $big_filepath_text = Xcache_engine_file::$cache_path . md5($key) . '.txt';
        $File_txt = File::instance($big_filepath_text);
        if ($File_json->exists) {
            if (filemtime($File_json->path) + $age > time()) {
                return $File_json->get_json();
            }
        } else if ($File_txt->exists) {
            if (filemtime($File_txt->path) + $age > time()) {
                return $File_txt->get_content();
            }
        } else {
            $cache_filepath = Xcache_engine_file::$cache_path . 'variables.json';
            $cache = File::instance($cache_filepath)->get_json();
            if (isset($cache[$key])) {
                $value = $cache[$key][0];
                $time = $cache[$key][1];
                if ($time + $age > time()) {
                    return $value;
                }
            }
        }
        return null;
    }

    public static function set($key, $value, $validate) {
        self::ensure_cache_folder();
        $key = @strval($key);
        $cache_filepath = Xcache_engine_file::$cache_path . 'variables.json';
        $cache = File::instance($cache_filepath)->get_json();
        if (!$cache) {
            $cache = array();
        }
        if ($validate) {
            $value = Validate::strict($validate);
        }
        $cache[$key] = array($value, time());
        File::_save_file($cache_filepath, json_encode($cache));
    }

    public static function set_big($key, $value, $validate) {
        self::ensure_cache_folder();
        $key = @strval($key);
        if ($validate) {
            $value = Validate::strict($validate);
        }
        if (is_string($value)) {
            $big_filepath = Xcache_engine_file::$cache_path . md5($key) . '.txt';
        } else {
            $big_filepath = Xcache_engine_file::$cache_path . md5($key) . '.json';
            $value = json_encode($value);
        }
        File::_save_file($big_filepath, $value);
    }

    public static function ensure_cache_folder() {
        Utilities::ensure_structure(Xcache_engine_file::$cache_path);
    }

}

Xcache_engine_file::$cache_path = PROJECT_ROOT . '_xtreme_cache/xcache/';
