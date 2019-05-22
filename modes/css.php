<?php

Xtreme::$App->config['mime'] = 'text/css';

$content = Xtreme::assets_file('css');

Response::deliver($content);
