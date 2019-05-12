<?php

$response = array(
    'status' => 400,
    'response' => array(),
    'errors' => array(),
    'error_code' => 0
);

if (isset(Xtreme::$App->config['file']) && is_string(Xtreme::$App->config['file'])) {
    $File_ajax_trylist = array(
        PROJECT_ROOT . Xtreme::$App->config['file'],
        Xtreme::$App->config['file'],
    );
} else {
    $File_ajax_trylist = array(
        PROJECT_ROOT . Request::$requested_clean_path,
        PROJECT_ROOT . str_replace('.html', '.php', Request::$requested_clean_path),
    );
}

$File_ajax = File::instance_of_first_existing_file($File_ajax_trylist);

if ($File_ajax->exists) {
    include $File_ajax->path;
}

Xtreme::$App->config['mime'] = 'application/json';
Response::deliver(json_encode($response));
