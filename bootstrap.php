<?php

define('DIR_XtremeCore', str_replace('\\', '/', __DIR__) . '/');
define('DIR_XtremeCore_classes', DIR_XtremeCore . 'classes/');

define('ROOT', DIR_XtremeCore);
define('PROJECT_ROOT', str_replace('xpm/library/xtreme_core/', '', ROOT));

define('Xtreme_cache', PROJECT_ROOT . '_xtreme_cache/');

if (isset($_ENV['environment'])) {
    define('ENV', $_ENV['environment']);
} else if (isset($_ENV['env'])) {
    define('ENV', $_ENV['env']);
} else {
    $ENV_dev = (bool) preg_match('/(192\.168\.\d+\.\d+|localhost|.+\.docker)/i', $_SERVER['SERVER_NAME']);
    define('ENV', $ENV_dev ? 'dev' : 'live');
}

include_once DIR_XtremeCore_classes . 'file.class.php';

foreach (File::ls(DIR_XtremeCore_classes, true, true) as $class_filepath) {
    if (!strstr($class_filepath, 'file.class.php')) {
        include_once $class_filepath;
    }
}

include_once DIR_XtremeCore . 'ensure_functions.php';

Request::init();
define('BASEURL', "http" . (is_https() ? 's' : '') . "://" . $_SERVER['SERVER_NAME'] . '/' . Request::$url_path_to_script);
Xtreme::init();

Xtreme::start_mode();
