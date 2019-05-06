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
        $File_app_trylist = File::_create_try_list('app', array('hjson', 'json'));
        $File_app = File::instance_of_first_existing_file($File_app_trylist);
        self::$app_config = $File_app->get_json();
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
        return trim($content);
    }

}

Xtreme::$version = file_get_contents(ROOT . 'version');
