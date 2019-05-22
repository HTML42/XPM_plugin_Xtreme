<?php

class Xcache {

    /** Valid values are: file, redis */
    private static $engine = 'file';
    
    public static $enabled = true;

    /**
     * 
     * @param string $engine | file, redis
     */
    public static function engine($engine) {
        switch ($engine) {
            case 'file':
            case 'files':
                self::$engine = 'file';
                break;
            case 'redis':
                self::$engine = 'redis';
                break;
            default:
                self::$engine = 'file';
        }
    }
    
    public static function fileengine_path($absolute_path) {
        Xcache_engine_file::$cache_path = $absolute_path;
    }

    public static function get($key, $age = 300) {
        if(!self::$enabled) {
            return null;
        }
        //
        $return = null;
        switch(self::$engine) {
            case 'file':
                $return = Xcache_engine_file::get($key, $age);
                break;
            case 'redis':
                $return = Xcache_engine_redis::get($key, $age);
                break;
        }
        return $return;
    }

    public static function set($key, $value, $validate = false) {
        if(!self::$enabled) {
            return null;
        }
        //
        $return = null;
        switch(self::$engine) {
            case 'file':
                $return = Xcache_engine_file::set($key, $value, $validate);
                break;
            case 'redis':
                $return = Xcache_engine_redis::set($key, $value, $validate);
                break;
        }
        return $return;
    }

    public static function set_big($key, $value, $validate = false) {
        if(!self::$enabled) {
            return null;
        }
        //
        $return = null;
        switch(self::$engine) {
            case 'file':
                $return = Xcache_engine_file::set_big($key, $value, $validate);
                break;
            case 'redis':
                $return = Xcache_engine_redis::set($key, $value, $validate);
                break;
        }
        return $return;
    }

}
