<?php
namespace Appkita\PHPAuth;
use METHOD;
use Appkita\PHPAuth\Type\Key;
use Appkita\PHPAuth\Type\Basic;
use Appkita\PHPAuth\Type\Digest;
use Appkita\PHPAuth\Type\Token;

final class Authentication {
    /**
     * @var string $_error
     */
    private $_error;
    /**
     * @var mixed $_data
     */
    private $_data;
     /**
     * @var array $_config
     */
    private $_config = [];
    
    /**
     * build configuration from api before start
     */
    function __construct($config = null) {
        if (!empty($config)) {
            if (\is_array($config)) {
                $this->_config = $config;
            } else if (is_object($config)) {
                $this->_config = (array) $config;
            }
        }
    }
    /**
     * @return $_error
     */
    public function error() {
        return $this->_error;
    }

    /**
     * Call authentication method
     * @var Method|| String $class
     * @var function $callback
     * @var array $args default []
     */
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

    /**
     * Generate Token JWT
     * @var mixed $data
     */
   public function token($data) {
        $tkn = new Token($this->_config);
        return $tkn->encode($data);
    }
}