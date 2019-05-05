<?php

class Xtreme {

    public static $version;

}

Xtreme::$version = file_get_contents($DIR . 'version');
