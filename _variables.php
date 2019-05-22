<?php

define('DIR_XtremeCore', str_replace('\\', '/', __DIR__) . '/');
define('DIR_XtremeCore_classes', DIR_XtremeCore . 'classes/');

define('ROOT', DIR_XtremeCore);
define('PROJECT_ROOT', str_replace('xpm/library/xtreme_core/', '', ROOT));

define('Xtreme_cache', PROJECT_ROOT . '_xtreme_cache/');

define('HOUR', 3600);
define('DAY', HOUR * 24);
define('WEEK', DAY * 7);
define('MONTH', DAY * 30);

if (isset($_ENV['environment'])) {
    define('ENV', $_ENV['environment']);
} else if (isset($_ENV['env'])) {
    define('ENV', $_ENV['env']);
} else {
    $ENV_dev = (bool) preg_match('/(192\.168\.\d+\.\d+|localhost|.+\.docker)/i', $_SERVER['SERVER_NAME']);
    define('ENV', $ENV_dev ? 'dev' : 'live');
}
