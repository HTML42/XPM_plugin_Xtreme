<?php

Xtreme::$App->config['mime'] = Utilities::mime_content_type_by_filename(Request::$requested_clean_path);

$content = Xtreme::assets_file();

Response::deliver($content);
