<?php 
use PHPUnit\Framework\TestCase;
use \Appkita\PHPAuth\METHOD;

final class AuthTest extends TestCase
{
    private $auth = null;

    function __construct() {
        parent::__construct();
        $config = [
            'allowed_key_parameter' => ['get', 'post', 'json'],
            'key_header'=>'x-app-key',
            'key'=>'123123',
            'data'=>'email',
            'timeout'=>1200,
            'iss'=>'test',
            'aud'=>'test'
        ];
        $this->auth = new \Appkita\PHPAuth\Authentication($config);
    }

    public function testKey() : void {
        //set Header KEY
        $_SERVER['HTTP_X_API_KEY'] = '123456';
       $cek = $this->auth->auth(METHOD::KEY, function($key) {
            if ($key === '123456') {
                return 'sukses';
            } else {
                return 'gagal';
            }
        });
        $this->expectOutputString($cek);
        print ($cek);
    }

    public function testBasic() : void {
        $_SERVER['PHP_AUTH_USER'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = '123456';
        $cek = $this->auth->auth(METHOD::BASIC, function($username, $password) {
            $hasil = [];
            if ($username !== 'user') {
                $hasil['username'] = 'sama';
            } else {
                $hasil['username'] = 'salah';
            }
            if ($password !== '123456') {
                $hasil['password'] = 'sama';
            } else {
                $hasil['password'] = 'salah';
            }
            return json_encode($hasil);
        });
        $this->expectOutputString($cek);
        print $cek;
    }

    public function testDigest() : void {
        $_SERVER['PHP_AUTH_DIGEST'] = 'user';
        $_SERVER['PHP_AUTH_PW'] = '123456';
        $cek = $this->auth->auth(METHOD::DIGEST, function($username, $password) {
            if ($username !== 'user') {
                return false;
            }
            return ['username'=>$username, 'password'=>$password];
        });
        if ($cek) {
            $cek = 'sukses';
        } else {
            $cek = 'gagal';
        }
        $this->expectOutputString($cek);
        print $cek;
    }

    public function testToken() : void {
        $_SERVER['HTTP_AUTHORIZATION'] = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczovL2FwcC1raXRhLmNvbSIsImF1ZCI6Imh0dHBzOi8vYXBwLWtpdGEubmV0Iiwic3ViIjoiMTIzNDU2Nzg5MCIsInVzZXJuYW1lIjoidXNlciIsImlhdCI6MTUxNjIzOTAyMiwiZXhwIjoxNzc4MjIyMjIyfQ.ZQ8yoZfdv5GeT2aF3m72FfZCAkk1HwbIqJC4_GHbxSc';
        $cek = $this->auth->auth(METHOD::TOKEN, function($username) {
            if ($username === 'user') {
                return 'sukses';
            } else {
                return 'gagal';
            }
        });
        $this->expectOutputString($cek);
        print $cek;
    }
}