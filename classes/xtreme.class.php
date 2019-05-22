<?php

class Xtreme {

    /** File::xtreme_core/version */
    public static $version;

    /** File::/app.hjson */
    public static $app_config;

    /** new App() */
    public static $App;

    /** xtreme_core/modes/ */
    public static $mode = null;

    /** Init Xtreme-Class */
    public static function init() {
        $package_apps = array();
        foreach (XPM_Packages::$package_list as $package_name) {
            $package_local_path = XPM::_ensure_trailing_slash(XPM_DIR_library) . $package_name . '/';
            $File_packageapp = File::instance_of_first_existing_file(array(
                        $package_local_path . '/app_config.json',
                        $package_local_path . '/app_config.hjson',
            ));
            if ($File_packageapp->exists) {
                $package_apps = $package_apps + $File_packageapp->get_json();
            }
        }
        //
        $File_app_trylist = File::_create_try_list('app', array('hjson', 'json'));
        $File_app = File::instance_of_first_existing_file($File_app_trylist);
        self::$app_config = $File_app->get_json() + $package_apps;
        //
        self::$App = new App();
        if (is_object(self::$App) && isset(self::$App->config) && isset(self::$App->config['mode']) &&
                is_string(self::$App->config['mode'])) {
            self::$mode = trim(self::$App->config['mode']);
        }
    }

    /** include Mode-File */
    public static function start_mode() {
        if (self::$mode) {
            $File_mode = File::instance(ROOT . 'modes/' . self::$mode . '.php');
            if ($File_mode->exists) {
                include $File_mode->path;
            }
        }
    }

    public static function deep_filelist($filename) {
        $trylist = array(PROJECT_ROOT . $filename);
        foreach (XPM_Packages::$package_list as $package_name) {
            array_push($trylist, XPM_DIR_library . $package_name . '/' . $filename);
        }
        array_push($trylist, $filename);
        return $trylist;
    }

    public static function deep_concat($files) {
        $content = '';
        if (isset($files) && is_array($files)) {
            $content = '';
            foreach ($files as $filepath) {
                if (Utilities::strstarts($filepath, 'pack.')) {
                    if (isset(Xtreme::$app_config[$filepath])) {
                        $content .= self::deep_concat(Xtreme::$app_config[$filepath]);
                    }
                } else {
                    $filelist = Xtreme::deep_filelist($filepath);
                    $File = File::instance_of_first_existing_file($filelist);
                    if ($File->exists) {
                        $content .= $File->get_content() . "\r\n";
                    } else {
                        $project_matches = glob(PROJECT_ROOT . $filepath);
                        foreach ($project_matches as $filematch) {
                            $content .= File::instance($filematch)->get_content() . "\r\n";
                        }
                    }
                }
            }
        }
        return trim($content);
    }

    public static function assets_file($type, $files = null) {
        if (is_null($files)) {
            $files = isset(Xtreme::$App->config['files']) ? Xtreme::$App->config['files'] : null;
        }
        if (strstr(Request::$requested_clean_path, '.min.')) {
            $cache_filepath = Xtreme_cache . Request::$requested_clean_path;
            if (is_file($cache_filepath)) {
                $content = file_get_contents($cache_filepath);
            } else {
                $cache_filepath_dir = implode('/', array_slice(explode('/', $cache_filepath), 0, -1));
                Utilities::ensure_structure($cache_filepath_dir);
                $content = isset($files) ? Xtreme::deep_concat($files) : '';
                //
                if ($type == 'image') {
                    $content = Xmin::image($content);
                } else if ($type == 'css') {
                    $content = Xless::compile_by_content($content, true);
                } else if ($type == 'js') {
                    $content = Xjs::compile_by_content($content);
                }
                //
                file_put_contents($cache_filepath, $content);
            }
        } else {
            $content = isset($files) ? Xtreme::deep_concat($files) : '';
            if ($type == 'image') {
                
            } else if ($type == 'css') {
                $content = Xless::compile_by_content($content, true);
            } else if ($type == 'js') {
                
            }
        }
        return $content;
    }

    public static function include_hook($hook_name) {
        if (isset($hook_name) && is_string($hook_name)) {
            $filepath_trylist = array(
                PROJECT_ROOT . 'hooks/' . $hook_name . '.php',
            );
            foreach ($filepath_trylist as $filepath) {
                $File = File::instance($filepath);
                if ($File->exists) {
                    include $File->path;
                }
            }
        }
    }

}

Xtreme::$version = file_get_contents(ROOT . 'version');
