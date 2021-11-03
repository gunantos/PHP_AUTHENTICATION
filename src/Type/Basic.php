<?php
/**
 * @author [Gunanto Simamora]
 * @website [https://app-kita.com]
 * @email [gunanto.simamora@gmail.com]
 * @create date 2021-05-06 12:55:49
 * @modify date 2021-05-06 12:55:49
 * @desc [Authentication basic]
 */

namespace Appkita\PHPAuth\Type;

class Basic {
    private $username_key = 'username';
    private $password_key = 'password';

    function __construct(Array $config = []) {
        if (isset($config['basic_auth'])) {
            $cfg = $config['basic_auth'];
            if (isset($cfg['username_key']) && !empty($cfg['username_key'])) {
                $this->username_key = $cfg['username_key'];
            }
            if (isset($cfg['password_key']) && !empty($cfg['password_key'])) {
                $this->password_key = $cfg['password_key'];
            }
        }
    }

    public function decode(callable $callback) {
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $username = '';
        $password = '';
        if (!$has_supplied_credentials) {
            $username = isset($_POST[$this->username_key]) ? $_POST[$this->username_key] : '';
            $password = isset($_POST[$this->password_key]) ? $_POST[$this->password_key] : '';
            if (empty($username) || empty($password)) {
                $json = @file_get_contents('php://input');
                $data = \json_decode($json);
                if (isset($data->{$this->username_key})) {
                    $username = $data->{$this->username_key};
                }
                if (isset($data->{$this->password_key})) {
                    $password = $data->{$this->password_key};
                }
            }
        } else {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        }
        if (empty($username) || empty($password)) {
            return false;
        }
        return \call_user_func($callback, $username, $password);
    }
}