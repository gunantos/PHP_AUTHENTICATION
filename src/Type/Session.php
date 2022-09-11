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

class Session {
    /**
     * @var string $session_id
     */
    private $session_id;
    /**
     * @var string $session_key
     */
    private $session_key = 'PHP_AUTH_SESSION';

    /**
     * get configuration file
     */
    function __construct(Array $config = []) {
        if (isset($config['session_key'])) {
            $cfg = $config['session'];
            if (isset($cfg['session_key']) && !empty($cfg['session_key'])) {
                $this->session_key = $cfg['session_key'];
            }
        }
    }

    /**
     * decode 
     * get session and return
     * @var callback $callback
     * @return mixed $result default false
     */
    public function decode(callable $callback) {
        $sess = false;
        if (isset($_SESSION[$this->session_key])) {
            if (!empty($_SESSION[$this->session_key])) {
                $sess = $_SESSION[$this->session_key];
            }
        }
        return \call_user_func($callback, false);
    }
}