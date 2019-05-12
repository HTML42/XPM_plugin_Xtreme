<?php

$File_page = File::instance_of_first_existing_file(array(
    PROJECT_ROOT . 'pages/' . Request::$requested_clean_path,
    PROJECT_ROOT . 'pages/' . str_replace('.html', '.php', Request::$requested_clean_path),
));

if ($File_page->exists) {
    Xtreme::$App->page_content = $File_page->get_content();
}

$website_content = Xtreme::$App->website();

Response::deliver($website_content);
