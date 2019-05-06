<?php

class Validate {

    public static function strict($var) {
        if (is_array($var)) {
            $varTmp = array();
            foreach ($var as $varKey => $varVal) {
                $varTmp[$varKey] = self::strict($varVal);
            }
            $var = $varTmp;
        } else {
            $var = strval($var);
            $var = strip_tags($var);
            $var = stripslashes($var);
        }
        return $var;
    }

    public static function strict_int($var) {
        if (is_array($var)) {
            $varTmp = array();
            foreach ($var as $varKey => $varVal) {
                $varTmp[$varKey] = self::strict_int($varVal);
            }
            $var = $varTmp;
        } else {
            $var = self::strict($var);
            $var = intval($var);
        }
        return $var;
    }

    public static function db($var) {
        if (is_array($var)) {
            foreach ($var as $index => $tmp) {
                $var[$index] = self::db($tmp);
            }
            return $var;
        }
        $var = strval($var);
        if (function_exists('mysql_real_escape_string')) {
            $var = mysql_real_escape_string($var);
        }
        return $var;
    }

    public static function htmlsafe($var) {
        $var = strval($var);
        $var = htmlspecialchars($var);
        return $var;
    }

    public static function is_email($emailAddress) {
        if (!is_string($emailAddress)) {
            return false;
        } else {
            return preg_match('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $emailAddress);
        }
    }

    public static function html_attr_safe($input) {
        if (is_string($input) || is_numeric($input)) {
            return $input;
        } else {
            return str_replace('"', '#doubleqoute#', str_replace('\'', '#singleqoute#', json_encode($input)));
        }
    }

    public static function password_strength($passwordplain) {
        #1 - Average: "at least 6 letters ((capital or lower case) and at least 2 numbers"
        #2 - High: "at least 6 letters (capital or lower case), at least 2 numbers and 1 symbol (#,*,$,etc.)"
        #3 - Maximum: "at least 6 letters (at least 1 capital and 1 lower case), at least 2 numbers and 1 symbol (#,*,$,etc.)
        $level = str_replace('level', '', sys_param('passwordsecurity'));
        $check = true;
        $letters = preg_replace('/[^a-zA-Z]*/', '', $passwordplain);
        $digits = preg_replace('/[^0-9]*/', '', $passwordplain);
        $symbols = preg_replace('/[a-zA-Z0-9]*/', '', $passwordplain);
        if (false) { #Set this true if u want to see the debugging
            dump('Level: ' . $level);
            dump('Passwordplain: ' . $passwordplain);
            dump('Letters: ' . $letters);
            dump('Digits: ' . $digits);
            dump('Symbols: ' . $symbols);
        }
        switch ($level) {
            case 2:
                if (strlen($letters) < 6) {
                    $check = false;
                }
                if (strlen($digits) < 2) {
                    $check = false;
                }
                if (strlen($symbols) < 1) {
                    $check = false;
                }
                break;
            case 3:
                if (strlen($letters) < 6) {
                    $check = false;
                }
                if (strtoupper($letters) == $letters || strtolower($letters) == $letters) {
                    $check = false;
                }
                if (strlen($digits) < 2) {
                    $check = false;
                }
                if (strlen($symbols) < 1) {
                    $check = false;
                }
                break;
            default:
                if (strlen($letters) < 6) {
                    $check = false;
                }
                if (strlen($digits) < 2) {
                    $check = false;
                }
        }
        return $check;
    }

    public static function validate_rule($data_value, $rule, $rule_value) {
        $success = true;
        switch ($rule) {
            case 'min-length':
            case 'minlength':
                if (strlen($data_value) < intval($rule_value)) {
                    $success = false;
                }
                break;
            case 'is-email':
            case 'isemail':
                if (!self::is_email($data_value)) {
                    $success = false;
                }
                break;
            case 'equalto':
            case 'equal-to':
                if (isset($_GET[$rule_value])) {
                    $success = ($data_value == $_GET[$rule_value] || $data_value == self::strict($_GET[$rule_value]));
                } else if ($_POST[$rule_value]) {
                    $success = ($data_value == $_POST[$rule_value] || $data_value == self::strict($_POST[$rule_value]));
                } else {
                    $success = false;
                }
                break;
        }
        return $success;
    }

}
