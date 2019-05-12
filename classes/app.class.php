<?php

class App {

    public static $default = array(
        'mode' => 'page',
        'mime' => 'text/html',
        'last_change' => null,
        'encoding' => 'UTF-8',
        'base' => 'base.tpl',
    );
    public $config = null;
    public $config_raw;
    public $page_content = null;

    public function __construct() {
        $request_path_without_min = str_replace('.min.', '.', Request::$requested_clean_path);
        if (isset(Xtreme::$app_config[Request::$requested_clean_path])) {
            $this->config_raw = Xtreme::$app_config[Request::$requested_clean_path];
            $this->config = $this->config_raw + self::$default;
            if (isset(Xtreme::$app_config['_default']) && is_array(Xtreme::$app_config['_default'])) {
                $this->config = $this->config + Xtreme::$app_config['_default'];
            }
        } else if (isset(Xtreme::$app_config[$request_path_without_min])) {
            $this->config_raw = Xtreme::$app_config[$request_path_without_min];
            $this->config = $this->config_raw + self::$default;
            if (isset(Xtreme::$app_config['_default']) && is_array(Xtreme::$app_config['_default'])) {
                $this->config = $this->config + Xtreme::$app_config['_default'];
            }
        }
        //
        if (in_array(File::_ext(Request::$requested_clean_path), array('jpg', 'png', 'gif', 'webp'))) {
            $this->config = array(
                'mode' => 'image',
                'files' => array(
                    Request::$requested_clean_path,
                    $request_path_without_min,
                )
                    ) + self::$default;
        }
        if (!is_array($this->config) &&
                is_file(PROJECT_ROOT . Request::$requested_clean_path) || is_file(PROJECT_ROOT . $request_path_without_min) &&
                in_array(File::_ext(Request::$requested_clean_path), array('css', 'js'))) {
            $this->config = array(
                'mode' => File::_ext(Request::$requested_clean_path),
                'files' => array(
                    Request::$requested_clean_path,
                    $request_path_without_min,
                )
                    ) + self::$default;
        }
        //
        if (is_array($this->config)) {
            if (isset($this->config_raw['301']) || isset($this->config_raw['302'])) {
                $this->config['mode'] = 'redirect';
            }
            //
            if (in_array($this->config['mode'], array('js', 'css', 'image'))) {
                $this->config['base'] = 'templates/base_clean.tpl';
            }
        }
    }

    public function website() {
        $webite_content = '';
        $File_base_trylist = File::_create_try_list($this->config['base'], array(), array('templates/'));
        $File_base = File::instance_of_first_existing_file($File_base_trylist);
        if ($File_base->exists) {
            $webite_content = $File_base->get_content();
            if (is_string($this->page_content) && strstr($webite_content, '##yield##')) {
                $webite_content = str_replace('##yield##', $this->page_content, $webite_content);
            }
            //
            $webite_content = Utilities::template($webite_content);
        }
        return $webite_content;
    }

}
