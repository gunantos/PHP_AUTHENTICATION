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
    private $allowed_key_parameter = ['get', 'post', 'json'];

    function __construct(Array $config = []) {
        foreach($config as $key => $value) {
           $key = \strtolower($key);
           if (isset($this->$key)) {
                $this->$key = $value;
           }
       }
    }

    private function getParameter(string $method) {
        $keyname = \str_replace(' ', '', $this->key_header);
        switch(\strtolower(\str_replace(' ', '', $method))) {
            case 'get':
                return isset($_GET[$keyname]) ? $_GET[$keyname] : '';
            break;
            case 'post':
                return isset($_POST[$keyname]) ? $_POST[$keyname] : '';
            break;
            case 'json':
                $json = \file_get_contents('php://input');
                $data = json_decode($json);
                if (isset($data->{$keyname})) {
                    return $data->{$keyname};
                } else {
                    return '';
                }
            break;
            default:
               return '';
        }
    }

    private function getKey() {
        $keyname = str_replace(' ', '', $this->key_header);
        $keyname = strtoupper(str_replace('-', '_', $keyname));
        $key_value = isset($_SERVER['HTTP_'. $keyname]) ? $_SERVER['HTTP_'. $keyname] : '';
        if (is_string($this->allowed_key_parameter)) {
            $this->allowed_key_parameter = [$this->allowed_key_parameter];
        }
        $i = 0;
        while(empty($key_value) && $i < \sizeof($this->allowed_key_parameter)) {
            $param = $this->allowed_key_parameter[$i];
			$param = strtolower($param);
            $get = $this->getParameter($param);
			$key_value = !empty($get) ? $get : $key_value;
            $i++; 
		}

        return $key_value;
    }

    public function decode(callable $callback, $config=[]) {
        $key = $this->getKey();
        if (empty($key)) {
            return false;
        }
        return \call_user_func($callback, $key, $config);
    }
}