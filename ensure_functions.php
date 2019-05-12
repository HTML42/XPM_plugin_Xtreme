<?php

//Shortcuts
function debug($var, $height = 'auto', $width = 'auto') {
    Utilities::dump($var, $height, $width);
}

function gz_compress($content) {
    return Utilities::gz_compress($content);
}

function css_active($param) {
    return Utilities::css_active($param);
}

//Maybe Missing Functions
if (!function_exists('boolval')) {

    function boolval($val) {
        return ($val == true || $val == 1);
    }

}
if (!function_exists('mime_content_type')) {

    function mime_content_type($filename) {
        return Utilities::mime_content_type_by_filename($filename);
    }

}

function is_https() {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https');
}
