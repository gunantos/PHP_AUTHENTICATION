<?php
/**
 * @author [Gunanto Simamora]
 * @website [https://app-kita.com]
 * @email [gunanto.simamora@gmail.com]
 * @create date 2021-05-06 12:55:49
 * @modify date 2021-05-06 12:55:49
 * @desc [Authentication API KEY]
 */

namespace Appkita\PHPAuth\Type;

class Key 
{  
    private $key_header = 'X-API-KEY';

    function __construct(Array $config = []) {
        foreach($config as $key => $value) {
           $key = \strtolower($key);
           if (isset($this->$key)) {
                $this->$key = $value;
           }
       }
    }

    public function decode(callable $callback) {
        $keyname = str_replace(' ', '', $this->key_header);
        $keyname = strtoupper(str_replace('-', '_', $keyname));
        if (!isset($_SERVER['HTTP_'. $keyname])) {
            return false;
        }
        if (empty($_SERVER['HTTP_'. $keyname])) {
            return false;
        }
        $key = $_SERVER['HTTP_'. $keyname];
        return \call_user_func($callback, $key);
    }
}