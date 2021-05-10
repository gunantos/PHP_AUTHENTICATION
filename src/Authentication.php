<?php
namespace Appkita\PHPAuth;
use METHOD;

final class Authentication {

    private $_error;
    private $_data;
    private $_config = [];
    
    function __construct($config = null) {
        if (!empty($config)) {
            if (\is_array($config)) {
                $this->_config = $config;
            }
        }
    }

    public function error() {
        return $this->_error;
    }

    public function auth($class, callable $callback) {
        $class = strtolower($class);
        $class = ucwords($class);
        if (!class_exists($class)) {
            set_error_handler(function ($servirity, $class) {
                 throw new \ErrorException('Class not found or '. $class, 0, $servirity, __DIR__.DIRECTORY_SEPARATOR.'Authentication.php', 22);
            });
        }
        $cll = '\\Appkita\\PHPAuth\\Type\\'. $class;
        $cls = new $cll($this->_config);
        return $cls->decode($callback);
    }
}