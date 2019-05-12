<?php

/**
 * Collection of Functions
 *
 * @author Paspirgilis
 */
class Utilities {

    public static function dump($var, $height = 'auto', $width = 'auto') {
        $backtrace = debug_backtrace();
        $file = 'Unknown';
        $line = 'Unknown';
        if (count($backtrace) > 1 && isset($backtrace[1]['function']) && in_array($backtrace[1]['function'], array('debug'))) {
            $file = '<span title="Through a debug from: ' . $backtrace[0]['file'] . '">' . $backtrace[1]['file'] . '</span>';
            $line = $backtrace[1]['line'];
        } else if (isset($backtrace[0]['file']) && isset($backtrace[0]['line'])) {
            $file = $backtrace[0]['file'];
            $line = $backtrace[0]['line'];
        }
        echo '<pre style="' .
        'word-break:break-word;border: 1px dashed #BBB;background-color: #CCC;padding:10px;color:#333;' . ($height == 'auto' ? '' : 'overflow-y:scroll;') .
        'height:' . ($height == 'auto' ? 'auto' : $height . 'px') . ';width:' . (strstr($width, '%') || strstr($width, 'px') ? $width : (strstr($width, 'auto') ? 'auto' : $width . 'px')) .
        ';' . ($width != '100%' ? 'margin: 0 auto 10px;' : '') . '">';
        echo '<span style="display: block; margin-bottom: 5px; font-weight: 700;">' . $file . ' (line ' . $line . '):</span>';
        ob_start();
        var_dump($var);
        echo htmlspecialchars(ob_get_clean());
        echo '</pre>';
    }

    public static function dump_class($class) {
        if (is_string($class)) {
            self::dump(get_class_vars($class));
        }
    }

    public static function strstarts($string, $start) {
        if (!is_string($string) || !is_string($start)) {
            return false;
        }
        return substr($string, 0, strlen($start)) == $start;
    }

    public static function strends($haystack, $needle) {
        $length = strlen($needle);
        return $length === 0 || (substr($haystack, -$length) === $needle);
    }

    public static function redirect($url = '/', $status = 200) {
        while (ob_get_level() > 1) {
            ob_end_clean();
        }
        Response::header('Location: ' . $url, $status);
        Response::header('Refresh:0; url=' . $url);
        die();
    }

    public static function mime_content_type_by_filename($filename) {
        $mime_types = array(
            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'hjson' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',
            'manifest' => 'manifest+json',
            'webmanifest' => 'manifest+json',
            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',
            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            // Fonts
            'eot' => 'application/vnd.ms-fontobject',
            'woff' => 'application/font-woff',
            'woff2' => 'application/font-woff2',
            'ttf' => 'application/x-font-truetype',
            'svg' => 'image/svg+xml',
            'otf' => 'application/x-font-opentype',
        );

        $f_explode = explode('.', $filename);
        $f_pop_explode = array_pop($f_explode);
        $ext = strtolower($f_pop_explode);
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        } elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        } else {
            return 'application/octet-stream';
        }
    }

    public static function gz_compress($content) {
        if (strstr($_SERVER['HTTP_USER_AGENT'], 'W3C_Validator') !== false ||
                strstr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false ||
                !function_exists('ob_gzhandler') ||
                ini_get('zlib.output_compression') === false) {
            return $content;
        } else {
            Response::header("Content-Encoding: gzip");
            return gzencode($content);
        }
    }

    public static function ensure_structure($folders) {
        if (is_string($folders)) {
            $folders = array($folders);
        }
        if (is_array($folders)) {
            foreach ($folders as $folder) {
                $parts = explode('/', $folder);
                $parts = array_filter($parts, 'strlen');
                $current_folder_path = (Utilities::strstarts($folder, '/') ? '/' : '');
                foreach ($parts as $part) {
                    $current_folder_path .= $part . '/';
                    //
                    if (!is_dir($current_folder_path)) {
                        File::_create_folder($current_folder_path);
                    }
                }
            }
        }
    }

    public static function css_active($param) {
        $is_active = false;
        if (!is_array($param)) {
            $param = array($param);
        }
        $request = str_replace(array('.html', '.php'), '', implode('/', Request::$requested_clean_path_array));
        foreach ($param as $check_string) {
            if (is_string($check_string)) {
                $check_string_naked = str_replace(array('.html', '.php'), '', $check_string);
                if ($check_string == $request) {
                    $is_active = true;
                } elseif ($check_string_naked == $request) {
                    $is_active = true;
                }
                if ($is_active) {
                    break;
                }
            }
        }
        return $is_active;
    }

    public static function rm_dir($dir) {
        if (is_dir($dir)) {
            if (substr($dir, strlen($dir) - 1, 1) != '/') {
                $dir .= '/';
            }
            $files = glob($dir . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::rm_dir($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($dir);
        }
    }

    /** Ensures that the given parameter is an Array */
    public static function ensure_array($anything) {
        if (is_object($anything)) {
            $anything = get_object_vars($anything);
        } else if (is_string($anything) || is_numeric($anything) || is_int($anything)) {
            $anything = array($anything);
        } else if (is_null($anything)) {
            $anything = array();
        } else if (is_bool($anything)) {
            $anything = array();
        } else if (!is_array($anything)) {
            $anything = (array) $anything;
        }
        return $anything;
    }

    /**
     * Copied from here: http://stackoverflow.com/questions/6672656/how-can-i-beautify-json-programmatically#15852465
     * ##Adapted by Mark Paspirgilis (shorter Code + Compatibility-optimizations
     * 
     * JSON beautifier
     * 
     * @param string    The original JSON string
     * @param   string  Return string
     * @param string    Tab string
     * @return string
     */
    public static function pretty_json($json, $ret = "\n", $ind = "    ") {

        if (is_array($json)) {
            $json = json_encode($json);
        } else if (!is_string($json)) {
            $json = '[]';
        }
        $beauty_json = '';
        $quote_state = FALSE;
        $level = 0;

        $json_length = strlen($json);

        for ($i = 0; $i < $json_length; $i++) {

            $pre = '';
            $suf = '';

            switch ($json[$i]) {
                case '"':
                    $quote_state = !$quote_state;
                    break;

                case '[':
                    $level++;
                    break;

                case ']':
                    $level--;
                    $pre = $ret . str_repeat($ind, $level);
                    break;

                case '{':

                    if ($i - 1 >= 0 && $json[$i - 1] != ',') {
                        //$pre = $ret . str_repeat($ind, $level);
                    }

                    $level++;
                    $suf = $ret . str_repeat($ind, $level);
                    break;

                case ':':
                    $suf = ' ';
                    break;

                case ',':

                    if (!$quote_state) {
                        $suf = $ret . str_repeat($ind, $level);
                    }
                    break;

                case '}':
                    $level--;

                case ']':
                    $pre = $ret . str_repeat($ind, $level);
                    break;
            }

            $beauty_json .= $pre . $json[$i] . $suf;
        }

        return $beauty_json;
    }

    public static function og_image_src() {
        $facebook_image = BASEURL . 'images/facebook/' . str_replace('/', '_', App::$request) . '.jpg';
        if (isset(App::$config['facebook_image']) && strlen(App::$config['facebook_image'])) {
            $facebook_image = App::$config['facebook_image'];
        }

        if (!Utilities::strstarts($facebook_image, 'http://') && !Utilities::strstarts($facebook_image, 'https://')) {
            $facebook_image = BASEURL . $facebook_image;
        }
        return $facebook_image;
    }

    public static function ensure_html5_tags() {
        $ie_lte_8 = (bool) preg_match('/MSIE\ [6-8]/', $_SERVER['HTTP_USER_AGENT']);
        if ($ie_lte_8) {
            $js_code = '<script type="text/JavaScript">(function(e,t){function c(e,t){var n=e.createElement("p"),r=e.getElementsByTagName("head")[0]||e.documentElement;n.innerHTML="x<style>"+t+"</style>";return r.insertBefore(n.lastChild,r.firstChild)}function h(){var e=y.elements;return typeof e=="string"?e.split(" "):e}function p(e){var t=f[e[u]];if(!t){t={};a++;e[u]=a;f[a]=t}return t}function d(e,n,r){if(!n){n=t}if(l){return n.createElement(e)}if(!r){r=p(n)}var o;if(r.cache[e]){o=r.cache[e].cloneNode()}else if(s.test(e)){o=(r.cache[e]=r.createElem(e)).cloneNode()}else{o=r.createElem(e)}return o.canHaveChildren&&!i.test(e)&&!o.tagUrn?r.frag.appendChild(o):o}function v(e,n){if(!e){e=t}if(l){return e.createDocumentFragment()}n=n||p(e);var r=n.frag.cloneNode(),i=0,s=h(),o=s.length;for(;i<o;i++){r.createElement(s[i])}return r}function m(e,t){if(!t.cache){t.cache={};t.createElem=e.createElement;t.createFrag=e.createDocumentFragment;t.frag=t.createFrag()}e.createElement=function(n){if(!y.shivMethods){return t.createElem(n)}return d(n,e,t)};e.createDocumentFragment=Function("h,f","return function(){"+"var n=f.cloneNode(),c=n.createElement;"+"h.shivMethods&&("+h().join().replace(/[\w\-:]+/g,function(e){t.createElem(e);t.frag.createElement(e);return\'c("\'+e+\'")\'})+");return n}")(y,t.frag)}function g(e){if(!e){e=t}var n=p(e);if(y.shivCSS&&!o&&!n.hasCSS){n.hasCSS=!!c(e,"article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}"+"mark{background:#FF0;color:#000}"+"template{display:none}")}if(!l){m(e,n)}return e}var n="3.7.0";var r=e.html5||{};var i=/^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;var s=/^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;var o;var u="_html5shiv";var a=0;var f={};var l;(function(){try{var e=t.createElement("a");e.innerHTML="<xyz></xyz>";o="hidden"in e;l=e.childNodes.length==1||function(){t.createElement("a");var e=t.createDocumentFragment();return typeof e.cloneNode=="undefined"||typeof e.createDocumentFragment=="undefined"||typeof e.createElement=="undefined"}()}catch(n){o=true;l=true}})();var y={elements:r.elements||"abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output progress section summary template time video",version:n,shivCSS:r.shivCSS!==false,supportsUnknownElements:l,shivMethods:r.shivMethods!==false,type:"default",shivDocument:g,createElement:d,createDocumentFragment:v};e.html5=y;g(t)})(this,document)</script>';
            echo $js_code;
        }
    }

    public static function validate($string, $as_int = false) {
        $string = Validate::strict($string);
        if ($as_int) {
            $string = @intval($string);
        }
        return $string;
    }

    public static function remote_ip() {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        } else {
            return null;
        }
    }

    public static function template($content) {
        foreach (array('App') as $class_name) {
            if (strstr($content, '##' . $class_name . '.')) {
                if (preg_match_all('/\#\#' . $class_name . '\.(.+)\#\#/isU', $content, $matches)) {
                    if ($class_name == 'App') {
                        $Class = Xtreme::$App;
                        $Class_variables = $Class->config;
                    } else {
                        $Class = get_class($class_name);
                        $Class_variables = get_class_vars($Class);
                    }
                    foreach ($matches[1] as $variable_name) {
                        if (isset($Class_variables[$variable_name])) {
                            $regex = '/\#\#' . $class_name . '\.' . $variable_name . '\#\#/isU';
                            $content = preg_replace($regex, $Class_variables[$variable_name], $content);
                        }
                    }
                }
            }
        }
        if (strstr($content, '##template.') || strstr($content, '##Template.')) {
            if (preg_match_all('/\#\#template\.(.+)\#\#/isU', $content, $matches)) {
                foreach ($matches[1] as $template_filename) {
                    $File_template_trylist = File::_create_try_list($template_filename, array('php', 'html', 'tpl'), array('templates/'));
                    $File_template = File::instance_of_first_existing_file($File_template_trylist);
                    $regex = '/\#\#template\.' . $template_filename . '\#\#/isU';
                    if ($File_template->exists) {
                        $content = preg_replace($regex, $File_template->get_content(), $content);
                    } else {
                        $content = preg_replace($regex, '<!-- (Template missing: ' . $template_filename . ') -->', $content);
                    }
                }
            }
        }
        return $content;
    }

}
