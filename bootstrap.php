<?php

define('DIR_XtremeCore', str_replace('\\', '/', __DIR__) . '/');
define('DIR_XtremeCore_classes', DIR_XtremeCore . 'classes/');

define('ROOT', DIR_XtremeCore);
define('PROJECT_ROOT', str_replace('xpm/library/xtreme_core/', '', ROOT));

include_once DIR_XtremeCore_classes . 'file.class.php';

foreach (File::ls(DIR_XtremeCore_classes, true, true) as $class_filepath) {
    if (!strstr($class_filepath, 'file.class.php')) {
        include_once $class_filepath;
    }
}

include_once DIR_XtremeCore . 'ensure_functions.php';

Request::init();
Xtreme::init();

Xtreme::start_mode();
