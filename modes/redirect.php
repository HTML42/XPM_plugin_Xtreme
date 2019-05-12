<?php

//Natural Checks
foreach (array(301, 302) as $status_code) {
    $status_code_string = strval($status_code);
    if (isset(Xtreme::$App->config[$status_code_string])) {
        Response::header('Content-length: 0');
        Utilities::redirect(Xtreme::$App->config[$status_code_string], $status_code);
    }
}
