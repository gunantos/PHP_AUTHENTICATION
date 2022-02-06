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


class GoogleLogin {
    private $client;

    function __construct($configs) {
        $file = false;
        if (\is_array($configs)) {
            if (isset($configs['google'])) {
                $config = (object) $configs['google'];
            } else {
                $config = $configs;   
            }
        } else if (\is_object($configs)) {
            if (isset($configs->google)) {
                $config = (object) $configs->google;
            } else {
                $config = $configs;   
            }
        } else {
            $config = $configs;
            $file = true;
        }
        $this->init($config, $file);
    }

    private function init($config, $config_file) {
        $clientID = '';
        $clientSecret = '';
        $redirectUri = '';
        $scope = [];
            
        if ($config_file) {
            $this->client = new \Google\Client();
            $this->client->setAuthConfig($config);
        } else {
            if (isset($config->clientID)) {
                $clientID = $config->clientID;
            }
            
            if (isset($config->clientSecret)) {
                $clientSecret = $config->clientSecret;
            }
            
            if (isset($config->redirectUri)) {
                $redirectUri = $config->redirectUri;
            }
            if (isset($config->scope)) {
                $scope = $config->scope;
            }
            if (empty($clientID) || empty($clientSecret) || empty($redirectUri)) {
                die('Google Api not config. Go to https://console.developers.google.com/?pli=1 to information');
            }
            $this->client = new \Google\Client();
            $this->client->setClientId($clientID);
            $this->client->setClientSecret($clientSecret);
            $this->client->setRedirectUri($redirectUri);
            $this->client->setAccessType('offline');
            $this->client->setScopes([
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile'
            ]);
        }
    }

    public function urlLogin() {
        return $this->client->createAuthUrl();
    }

    public function decode($callback, $args = []) {
        $error = '';
        $user = (object) [];
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                if ($token['error'] == 'invalid_grant') {
                    return \call_user_func($callback, $user, $args, $token['error']);
                } else if (!empty($token->error)) {
                    return \call_user_func($callback, $user, $args, $token['error']);
                }
            } else if (!isset($token['access_token'])) {
                return \call_user_func($callback, $user, $args, 'access token not found');
            }
            $this->client->setAccessToken($token['access_token']);
            $googleauth = new \Google_Service_Oauth2($this->client);
            return \call_user_func($callback, $googleauth->userinfo->get(), $args, $error);
        } else {
            header('Location: '. $this->urlLogin, true);
            die();
        }
    }
}