<?php

Xtreme::$App->config['mime'] = 'text/javascript';

$content = Xtreme::assets_file('js');

Response::deliver($content);
