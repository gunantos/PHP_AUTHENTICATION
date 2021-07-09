<?php
/**
 * @author [Gunanto Simamora]
 * @website [https://app-kita.com]
 * @email [gunanto.simamora@gmail.com]
 * @create date 2021-05-06 12:55:49
 * @modify date 2021-05-06 12:55:49
 * @desc [Authentication digest]
 */
namespace Appkita\PHPAuth\Type;


class Digest {

    function __construct(Array $config = []) {
    }

    private function getDigest() {
        $digest = false;
        if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
            $digest = $_SERVER['PHP_AUTH_DIGEST'];
        } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']),'digest')===0)
                $digest = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
        }

        return $digest;
    }

    private function digestParse($digest) {
        $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
        $data = array();
        preg_match_all('@(\w+)=(?:(?:")([^"]+)"|([^\s,$]+))@', $digest, $matches, PREG_SET_ORDER);

        foreach ($matches as $m) {
            $data[$m[1]] = $m[2] ? $m[2] : $m[3];
            unset($needed_parts[$m[1]]);
        }

        return $needed_parts ? false : $data;
    }

    public function decode(callable $callback, $config=[]) {
        $digest = $this->getDigest();
        if (is_null($digest)) return false;
        $digestParts = $this->digestParse($digest);
        $db = [];
        if (\is_callable($callback)) {
            $db = \call_user_func($callback, $digestParts['username'], null, $config);
        } else {
            set_error_handler(function ($servirity, $class) {
                 throw new \ErrorException('You must insert callback in Digest', 0, $servirity, $class, 59);
            });
        }
        if (\is_array($db)) {
            $db = (object) $db;
        } else if (\is_string($db)) {
            $db = (object) ['username'=>$db, 'password'=>null];
        }else if (!$db) {
            return false;
        }
        if (isset($db->username) || isset($db->password)) {
            throw new \ErrorException('You must return array [username=>"", password=>""] or false from callback', 0, $servirity, $class, 71);
        }
        $A1 = md5("{$db->username}:{$this->realm}:{$db->password}");
        $A2 = md5("{$_SERVER['REQUEST_METHOD']}:{$digestParts['uri']}");    
        $validResponse = md5("{$A1}:{$digestParts['nonce']}:{$digestParts['nc']}:{$digestParts['cnonce']}:{$digestParts['qop']}:{$A2}");
        if ($validResponse !== $digestParts['response']) {
            return false;
        } else {
            return true;
        }
    }
}