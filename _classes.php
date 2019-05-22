<?php

foreach (scandir(DIR_XtremeCore_classes) as $class_filename) {
    if (!in_array($class_filename, array('.', '..'))) {
        include_once DIR_XtremeCore_classes . $class_filename;
    }
}
