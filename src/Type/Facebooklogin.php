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


class Facebooklogin {
    private $client;
    private $helper;
    private $facebook_login_url;

    function __construct($configs) {
        if (class_exists('\\Facebook\\Facebook')) {
            die('facebook/graph-sdk not installed. please install `composer require facebook/graph-sdk`');
        }
        $file = false;
        if (\is_array($configs)) {
            if (isset($configs['facebook'])) {
                $config = (object) $configs['facebook'];
            } else {
                $config = $configs;   
            }
        } else if (\is_object($configs)) {
            if (isset($configs->google)) {
                $config = (object) $configs->facebook;
            } else {
                $config = $configs;   
            }
        } else {
            $config = $configs;
            $file = true;
        }
        $this->init($config, $file);
    }
    
    private function throw_error($f = '') {
        switch ($f) {
            case '':
                 die('Facebook Api not config. Go to <a href="https://developers.facebook.com/docs/facebook-login/web/accesstokens">https://developers.facebook.com/docs/facebook-login/web/accesstokens</a> to information');
                 exit();
                 break;
            default: 
                die($f);
                break;                 
        }
    }
    private function init($config, $fromFile) {
        $clientID = ''; 
        $clientSecret = '';
        $redirectUri = '';
        $scope = [];
        $config_file = false;
        
        if ($fromFile) {
            if (!\file_exists($config)) {
                return $this->throw_error();
            }
            $json = file_get_contents($config);
            try {
                $config = \json_decode($json);
            } catch (Exception $e) {
                return $this->throw_error($e);
            }
        }

        if (isset($config->clientID)) {
            $clientID = $config->clientID;
        }
            
        if (isset($config->clientSecret)) {
            $clientSecret = $config->clientSecret;
        }
            
        if (isset($config->redirectUri)) {
            $redirectUri = $config->redirectUri;
        }
        if (empty($clientID) || empty($clientSecret) || empty($redirectUri)) {
            
                return $this->throw_error();
        }
        $this->client = new \Facebook\Facebook([
                'app_id'=>$clientID,
                'app_secret'=>$clientSecret,
                'persistent_data_handler'=>'session'
        ]);
        $this->helper  = $this->client->getRedirectLoginHelper();
        //'email', 'birthday', 'gender', 'hometown', 'location'
        $this->facebook_login_url = $this->helper->getLoginUrl($redirectUri, ['email', 'user_birthday', 'user_gender']);
    }

    public function urlLogin() {
        return $this->facebook_login_url;
    }

    public function decode($callback, $args = []) {
        
        $error = '';
        try {
            $accessToken = $this->helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {  
            $error = $e->getMessage();  
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {  
            $error = $e->getMessage();  
        } catch(Exception $e) {
            $error = $e;
        } finally {
            if (!empty($error)) {
                return \call_user_func($callback, (object) [], $args, $error);
            }
        }
        if (!isset($accessToken)) {
            return \call_user_func($callback, (object) [], $args, $error);
        }
        $oAuth2Client = $this->client->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        if (is_array($args)) {
            array_push($args, $tokenMetadata);
        }
        try {
            $scope = [
                'id', 'name', 'first_name', 'last_name', 'email', 'short_name', 'picture', 'birthday', 'gender', 'hometown', 'location'
            ];
            $profile_request = $this->client->get('/me?fields='. implode(',', $scope), $accessToken);
            $requestPicture = $this->client->get('/me/picture?redirect=false&height=200', $accessToken); 
            
            $profile = $profile_request->getGraphUser();
            $picture =  $requestPicture->getGraphUser();

            $user = ['photo_profile'=>$picture['url']];
            for($i = 0; $i < sizeof($scope); $i++) {
                if ($scope[$i] == 'picture'){
                    $user['picture'] = $profile->getProperty('picture')['url'];
                } else {
                    $user[$scope[$i]] = $profile->getProperty($scope[$i]);
                }
            }

            return \call_user_func($callback, (object) $user, $args, $error);
        } catch(Exception $e) {
            return \call_user_func($callback, (object) [], $args, $e);
        }
    }
}