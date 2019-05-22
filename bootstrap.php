<?php


Xtreme::include_hook('init_before');

Request::init();
define('BASEURL', "http" . (is_https() ? 's' : '') . "://" . $_SERVER['SERVER_NAME'] . '/' . Request::$url_path_to_script);
Xtreme::init();

Xtreme::include_hook('init_after');

Xtreme::start_mode();
