# PHP AUTHENTICATION

### _Simple Library PHP Authentication API_

<a href="https://app-kita.com" alt="app-kita, app kita"><img src="https://app-kita.com/img/logo-teks.965d24bf.png" width="100"></a><br>
[![Build Status](https://www.travis-ci.com/gunantos/PHP_AUTHENTICATION.svg?branch=main)](https://www.travis-ci.com/gunantos/PHP_AUTHENTICATION)

Sample Library PHP Authentication Restfull API

- Support Multi Authentication

## Installation

Dillinger requires [PHP](https://php.net/) v7.0+ to run.

Install composer follow [composer](https://getcomposer.org/download/)

```sh
composer require appkita/phpauth
```

or

Edit composer.json and update composer

```sh
{
    "require": {
        "appkita/phpauth": "^0.1.*"
    }
}
```

## Features

- API KEY Authentication
- JWT Authentication (Token)
- Basic Authentication
- Digest Authentication

## Using

_configuration_

```php
  $config = [
    'key_header'=>'X-API-KEY', //Delete if you not use API KEY
    //jwt Configuration
    'key'=>'key_JWT',
    'data'=>'username',
    'timeout'=>3600,
    'iss'=>'mydomain.com',
    'aud'=>'mydomain.com',
    'basic_auth'=>[
        'username_key'=>'email',
        'password_key'=>'password'
    ];
  ];
  $auth = new Appkita\PHPAuth\Authentication($config);
  //or
  use Appkita\PHPAuth;
  $auth = new Authentication($config);
  //or configuration default
  $auth = new Authentication();
```

```php
    $cek = $auth->auth(METHOD, callback);
```

_callback_ : is function to cek username or key you can set return or die
but if you using digest authentication you must return array

```php
return ['username'=>username, 'password'=>password];
```

```js
    method Support
    METHOD::Key = 'key',
    METHOD::Basic = 'basic',
    METHOD::Digest = 'digest'
    METHOD::Token = 'token' //is JWT Authentication

```

_Example_

### 1. KEY

```php
    $mykey = 'testingkey';
    $cek = $auth->auth(METHOD::KEY, function($key) {
        if ($key === $mykey) {
            return true;
        } else {
            return false;
        }
    });
```

### 2. BASIC

```php
    $myusername = 'testingkey';
    $mypassword = 'password';
    $cek = $auth->auth(METHOD::BASIC, function($username, $password) {
        if ($username == $myusername && $mypassword == $password) {
            return true;
        } else {
            return false;
        }
    });
```

### 3. DIGEST

```php
    $myusername = 'testingkey';
    $mypassword = 'password';
    $cek = $auth->auth(METHOD::DIGEST, function($username, $password) {
        if ($username == $myusername) {
            return ['username'=>$myusername, 'password'=>$mypassword];
        } else {
            return false;
        }
    });
```

### 4. TOKEN (JWT)

```php
    $myusername = 'testingkey';
    $cek = $auth->auth(METHOD::TOKEN, function($username) {
        if ($username == $myusername) {
            return true
        } else {
            return false;
        }
    });
```

## Development

Want to contribute? Great!

Open your favorite Terminal and run these commands.

First Tab:

```sh
git clone git@github.com:gunantos/PHP_AUTHENTICATION.git
```

Second Tab:

```sh
cd PHP_AUTHENTICATION
composer install
```

## License

MIT

# Sponsor

[Pay Coffe](https://sponsor.app-kita.net)
