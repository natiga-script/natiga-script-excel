<?php

/**
 * Security Functions Class
 * 
 * @author Abood Nour
 * @copyright 2017
 * @version 1.00
 * */

class Natiga_Security
{
    private static $csrf_duration = 1200; //20 minutes
    public static $csrf_token_name = 'csrf_token';

    /**
     * Generates random token
     * 
     * @return String
     *  returns hexadecimal representation of md5 hash of the generated token
     * */

    public static function generate_token()
    {
        if (function_exists('random_bytes'))
        {
            return md5(random_bytes(64));
        } else if (function_exists('openssl_random_pseudo_bytes'))
        {
            return md5(openssl_random_pseudo_bytes(64));
        } else
        {
            return md5(uniqid(rand(), true));
        }
    }
    
    /**
     * Generates CSRF token and store its value along with request time in $_SESSION global variable 
     * 
     * @return String
     *  returns generated CSRF token
     * */
    public static function generate_csrf_token()
    {
        if (isset($_SESSION))
        {
            if (!isset($_SESSION[self::$csrf_token_name]))
            {
                $_SESSION[self::$csrf_token_name] = array();
            }
            $token = self::generate_token();
            $_SESSION[self::$csrf_token_name][microtime(true)] = $token;
            return $token;
        }
    }

    /**
     * Validates if the provided CSRF token exists and hasn't expired
     * 
     * @param String $csrf_token
     * CSRF token you would like to validate
     * 
     * @return Bool
     *  returns true if CSRF token is valid, false otherwise
     * */
    public static function validate_csrf_token($csrf_token)
    {
        if (isset($_SESSION) && !empty($csrf_token) && is_string($csrf_token))
        {
            if (!empty($_SESSION[self::$csrf_token_name]) && is_array($_SESSION[self::$csrf_token_name]))
            {
                if (($time = array_search($csrf_token, $_SESSION[self::$csrf_token_name], true)) && (microtime(true) - $time) <= self::$csrf_duration)
                {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Parses provided parameter as string and returns its absolute value
     * 
     * @param mixed $var
     * Value to you would like to sanitize
     * 
     * @return int
     *  returns absolute integer value
     * */

    public static function toInt($var)
    {
        return abs(intval($var));
    }

    //attempt to sanitize PHP Database
    /**
     * Attempts to sanitize parameter to be safe to provide in PDO connection string by removing some special chars
     * 
     * @param String $DSN
     * Value to you would like to sanitize
     * 
     * @return string
     *  returns somewhat safe to use string
     * */
    public static function sanitize_PDO_DSN($DSN)
    {
        return str_ireplace(array(
            ';',
            '=',
            ' ',
            '"',
            '\''), '', $DSN);
    }

    /**
     * Removes any character that is not alphanumeric
     * 
     * @param String $str
     * Value to you would like to sanitize
     * 
     * @return string
     *  returns alphanumeric string
     * */
    public static function alphanum($str)
    {
        return preg_replace('#[^a-z0-9_\.]+#i', '', $str);
    }
    
    /**
     * Encodes HTML Special characters to be safe to use inside a tag attribute
     * 
     * @param String $str
     * Value to you would like to sanitize
     * 
     * @return string
     *  returns sanitized value
     * */
    public static function escapeAttribute($str)
    {
        $flag = (version_compare(PHP_VERSION, '5.4.0', '>')) ? ENT_QUOTES | ENT_HTML5 : ENT_QUOTES;
        return htmlentities($str, $flag, 'UTF-8');
    }
    
    /**
     * Encodes HTML Special characters to be safe to use inside of tags
     * 
     * @param String $str
     * Value to you would like to sanitize
     * 
     * @return string
     *  returns sanitized value
     * */
    public static function escapeHTML($str)
    {
        return self::escapeAttribute($str);
    }

    /**
     * Escapes Regular Expression special characters to be safe to use string in regular expressions
     * 
     * @param String $str
     * Value to you would like to sanitize
     * 
     * @return string
     *  returns sanitized value
     * */
    public static function escapeRegEx($str, $delimiter = null)
    {
        return preg_quote($str, $delimiter);
    }

    /**
     * Escapes `MySQL like` special chars to be safe to use with like operator 
     * 
     * @param String $str
     * Value to you would like to sanitize
     * 
     * @return string
     *  returns sanitized value
     * */
    public static function escapeLike($str)
    {
        return addcslashes($str, '_%\\');
    }
}

?>