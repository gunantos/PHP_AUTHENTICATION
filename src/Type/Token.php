<?php
/**
 * @author [Gunanto Simamora]
 * @website [https://app-kita.com]
 * @email [gunanto.simamora@gmail.com]
 * @create date 2021-05-06 12:55:49
 * @modify date 2021-05-06 12:55:49
 * @desc [Authentication Token JWT]
 */

namespace Appkita\PHPAuth\Type;
use \Firebase\JWT\JWT;

class Token
{
    private $key = '1klso1LMWnLKLQPzIksx#3';
    private $data = 'username';
    private $timeout = 3600;
    private $iss = 'https://app-kita.com';
    private $aud = 'https://app-kita.net';
    
    function __construct(Array $config = []) {
       foreach($config as $key => $value) {
           $key = \strtolower($key);
           if (isset($this->$key)) {
                $this->$key = $value;
           }
       }
    }


    public function decode(callable $callback, $args = []) {
        if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return false;
        }
        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            return false;
        }
        $decodedToken = $matches[1];
        if (! $decodedToken) {
            return false;
        }
        $decodedToken = JWT::decode($decodedToken, $this->key, ['HS256']);
        if (!$decodedToken) {
            return false;
        }
        if (!isset($decodedToken->iss) && !isset($decodedToken->aud)) {
            return false;
        }
        
        if ($decodedToken->iss != $this->iss|| $decodedToken->aud != $this->aud) {
            return false;
        }
        if (!isset($decodedToken->{$this->data})) {
             return false;
        }
        return \call_user_func($callback, $decodedToken->{$this->data}, $args);
    }

    public function encode($data) : string {
        $issuedAtTime = time();
        $tokenTimeToLive = $this->timeout ?? 3600;
        $tokenExpiration = $issuedAtTime + $tokenTimeToLive;

        $payload = [
            'iss' => $this->iss,
            'aud' => $this->aud,
            'iat' => $issuedAtTime,
            'exp' => $tokenExpiration,
            $this->data => $data
        ];
        if (!empty($data)){
            if (\is_array($data)) {
                $payload = array_merge($payload, $data);
            } else {
                $payload['data'] = $data;
            }
        }
        $jwt = JWT::encode($payload, $this->key);
        return $jwt;
    }
}