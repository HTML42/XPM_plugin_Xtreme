<?php

ini_set('short_open_tag', 1);
ini_set('magic_quotes_gpc', 1);
ini_set("memory_limit", "512M");

@session_start();

if (is_file('../../../hooks/start.php')) {
    include '../../../hooks/start.php';
}

include '_variables.php';
include DIR_XtremeCore . '_classes.php';
include DIR_XtremeCore . 'ensure_functions.php';

include '../../loader.php';

include 'bootstrap.php';
