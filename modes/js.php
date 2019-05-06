<?php

Xtreme::$App->config['mime'] = 'text/javascript';

$content = Xtreme::assets_file();

Response::deliver($content);
