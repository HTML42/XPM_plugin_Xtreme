<?php

Xtreme::$App->config['mime'] = 'text/css';

$content = isset(Xtreme::$App->config['files']) ? Xtreme::deep_concat(Xtreme::$App->config['files']) : '';

Response::deliver($content);
