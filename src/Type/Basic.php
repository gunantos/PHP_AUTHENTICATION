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

    function __construct(Array $config = []) {
    }

    public function decode(callable $callback, $config = []) {
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        if (!$has_supplied_credentials) {
            return false;
        }
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];
        return \call_user_func($callback, $username, $password, $config);
    }
}