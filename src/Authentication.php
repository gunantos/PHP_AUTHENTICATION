<?php
namespace Appkita\PHPAuth;
use METHOD;
use Appkita\PHPAuth\Type\Key;
use Appkita\PHPAuth\Type\Basic;
use Appkita\PHPAuth\Type\Digest;
use Appkita\PHPAuth\Type\Token;

final class Authentication {

    private $_error;
    private $_data;
    private $_config = [];
    
    function __construct($config = null) {
        if (!empty($config)) {
            if (\is_array($config)) {
                $this->_config = $config;
            } else if (is_object($config)) {
                $this->_config = (array) $config;
            }
        }
    }

    public function error() {
        return $this->_error;
    }

    public function auth($class, callable $callback, $args = []) {
        $class = strtolower($class);
        $class = ucwords($class);
        if (!class_exists($class)) {
            set_error_handler(function ($servirity, $class) {
                 throw new \ErrorException('Class not found or '. $class, 0, $servirity, __DIR__.DIRECTORY_SEPARATOR.'Authentication.php', 28);
            });
        }
        $cll = '\\Appkita\\PHPAuth\\Type\\'. $class;
        $cls = new $cll($this->_config);
        return $cls->decode($callback, $args);
    }

   public function token($data) {
        $tkn = new Token($this->_config);
        return $tkn->encode($data);
    }
}