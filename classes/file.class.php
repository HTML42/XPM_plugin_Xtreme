<?php

class File {

    public $path = null;
    public $exists = false;
    private static $_CACHE = array('instances' => array(), 'filenames' => array(), 'filefolders' => array());

    public function __construct($file_path = null) {
        if (is_string($file_path)) {
            $this->path = $file_path;
            $this->load_meta();
        }
    }

    /**
     * 
     * @param string $file_path
     * @return new File()
     */
    public static function instance($file_path) {
        if (!isset(self::$_CACHE['instances'][$file_path])) {
            $Class = get_called_class();
            self::$_CACHE['instances'][$file_path] = new $Class($file_path);
        }
        return self::$_CACHE['instances'][$file_path];
    }

    /**
     * Shurtcut.Method for 
     * @param string $file_path
     * @return new File()
     */
    public static function i($file_path) {
        $try_list = self::_create_try_list($file_path);
        return self::instance_of_first_existing_file($try_list);
    }

    /**
     * 
     * @param array/string $file_path
     * @return new File()
     */
    public static function instance_of_first_existing_file($file_pathes) {
        $Class = get_called_class();
        foreach ((array) $file_pathes as $file_path) {
            if (is_file($file_path)) {
                return $Class::instance($file_path);
            }
        }
        return new $Class();
    }

    public function name() {
        return self::_name($this->path);
    }

    public function folder() {
        return self::_folder($this->path);
    }

    public function ext() {
        return self::_ext($this->path);
    }

    public function load_meta() {
        if (is_string($this->path)) {
            $this->exists = is_file($this->path);
        }
    }

    public function get_json() {
        if ($this->exists) {
            $content = $this->get_content();

            //HJSON Support
            $content = preg_replace('|\/\*.*\*\/|U', '', $content);

            return @json_decode($content, true);
        } else {
            return null;
        }
    }

    public function get_content() {
        if ($this->exists) {
            if ($this->ext() == 'php') {
                ob_start();
                include $this->path;
                return ob_get_clean();
            } else {
                return file_get_contents($this->path);
            }
        } else {
            return '';
        }
    }

    //#Helpers


    public static function _create_folder($folderpath) {
        $folderpath = str_replace('/', DIRECTORY_SEPARATOR, $folderpath);
        mkdir($folderpath);
    }

    public static function _save_file($filepath, $content) {
        $filepath = str_replace('/', DIRECTORY_SEPARATOR, $filepath);
        file_put_contents($filepath, $content);
        @chmod($filepath, 0777);
    }

    public static function cp($source, $destination, $options = '') {
        $MethodOptions = new MethodOptions($options);
        $mop = $MethodOptions->parameter; // MethodOptions-Parameters (mop)
        $path = self::path($source);
        //
        if (is_dir($source)) {
            $folder = self::ls($source);
        } else if (is_file($source)) {
            $folder = array(self::_name($source));
        }
        //
        $destination = self::n($destination);
        //
        foreach ($folder as $item) {
            $itempath = $path . $item;
            if (is_file($itempath)) {
                if (!is_file($destination . $item) || $MethodOptions->p('f')) {
                    copy($itempath, $destination . $item);
                }
            } else if ($MethodOptions->p('r') && is_dir($itempath)) {
                +
                        @mkdir(self::n($destination . $item));
                self::cp(self::n($itempath), self::n($destination . $item), $options);
            }
        }
    }

    public static function ls($source, $fullpath = false, $only_files = false) {
        if (is_dir($source)) {
            $source = self::n($source);
            $folder = scandir($source);
            $folder = array_filter($folder, function($filename) {
                return $filename != '.' && $filename != '..';
            });
            if ($only_files) {
                $folder = array_filter($folder, function($filename) use ($source) {
                    return is_file($source . $filename);
                });
            }
            if ($fullpath) {
                foreach ($folder as &$filename) {
                    $filename = $source . $filename;
                }
            }
            return $folder;
        } else {
            return array();
        }
    }

    public static function path($source) {
        $path = explode('/', $source);
        $path = array_slice($path, 0, count($path) - 1);
        $path = implode('/', $path);
        return $path . '/';
    }

    public static function normalize_folder($source) {
        if (is_string($source)) {
            $source = trim($source);
            if (substr($source, -1) != '/') {
                $source .= '/';
            }
        }
        return $source;
    }

    public static function n($p) {
        return self::normalize_folder($p);
    }

    public static function _name($filepath) {
        if (!isset(self::$_CACHE['filenames'][$filepath])) {
            $filename = explode('/', $filepath);
            $filename = end($filename);
            self::$_CACHE['filenames'][$filepath] = $filename;
        }
        return self::$_CACHE['filenames'][$filepath];
    }

    public static function _folder($filepath) {
        if (!isset(self::$_CACHE['filefolders'][$filepath])) {
            $filename = self::_name($filepath);
            $filefolder = str_replace($filename, '', $filepath);
            self::$_CACHE['filefolders'][$filepath] = $filefolder;
        }
        return self::$_CACHE['filefolders'][$filepath];
    }

    public static function _ext($filepath) {
        if (!isset(self::$_CACHE['fileext'][$filepath])) {
            if (strstr($filepath, '.')) {
                $file_exts = explode('.', $filepath);
                self::$_CACHE['fileext'][$filepath] = end($file_exts);
            } else {
                self::$_CACHE['fileext'][$filepath] = false;
            }
        }
        return self::$_CACHE['fileext'][$filepath];
    }

    public static function _create_try_list($filename, $extensions = array(), $prepathes = false) {
        $list = array();
        $base_pathes = array(PROJECT_ROOT, ROOT);
        if (!in_array('', $extensions)) {
            array_push($extensions, '');
        }
        if (is_string($prepathes) && strlen($prepathes) > 0) {
            $prepathes = array($prepathes);
        }
        if (!is_array($prepathes)) {
            $prepathes = array('');
        }
        if (!in_array('', $prepathes)) {
            array_push($prepathes, '');
        }
        foreach ($base_pathes as $base_path) {
            foreach ($extensions as $extension) {
                if (strlen($extension) > 0 && !strstr($extension, '.')) {
                    $extension = '.' . $extension;
                }
                if (is_array($prepathes)) {
                    foreach ($prepathes as $prepath) {
                        array_push($list, $base_path . $prepath . $filename . $extension);
                    }
                }
            }
        }
        return $list;
    }

}
