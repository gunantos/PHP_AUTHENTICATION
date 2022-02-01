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

    function __construct($config) {
        $this->init($config);
    }

    private function init($config) {
        $clientID = '';
        $clientSecret = '';
        $redirectUri = '';
        $scope = [];
        $config_file = false;
         if (\is_array($config)) {
            if (isset($config->Google_clientID)) {
                $clientID = $config->Google_clientID;
            }
            
            if (isset($config->Google_clientSecret)) {
                $clientSecret = $config->Google_clientSecret;
            }
            
            if (isset($config->redirectUri)) {
                $redirectUri = $config->redirectUri;
            }
            if (isset($config->scope)) {
                $scope = $config->scope;
            }
            $config_file = true;
        } else {
            if (\file_exists($config)) {
                $config_file = true;
            }
        }
        if ($config_file) {
            $this->client = new \Google\Client();
            $this->client->setAuthConfig($config);
        } else {
            if (empty($clientID) || empty($clientSecret) || empty($redirectUri)) {
                die('Google Api not config. Go to https://console.developers.google.com/?pli=1 to information');
            }
            $this->client = new Google\Client();
            $this->client->setClientId($clientID);
            $this->client->setClientSecret($clientSecret);
            $this->client->setRedirectUri($redirectUri);
            if (\sizeof($scope) > 0) {
                for($i = 0; $i < sizeof($scope); $i++) {
                    $this->client->addScope($scope[$i]);
                }
            } else {
                $this->client->addScope("email");
                $this->client->addScope("profile");
            }
        }
    }

    public function urlLogin() {
        return $this->client->createAuthUrl();
    }

    public function decode($callback, $args = []) {
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($token['access_token']);
            $googleauth = new Google_Service_Oauth2($client);
            return \call_user_func($callback, $googleauth->userinfo->get(), $args);
        } else {
            header('Location: '. $this->urlLogin, true);
            die();
        }
    }
}