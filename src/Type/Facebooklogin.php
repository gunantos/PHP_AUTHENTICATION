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
            if (isset($config->FB_clientID)) {
                $clientID = $config->FB_clientID;
            }
            
            if (isset($config->FB_clientSecret)) {
                $clientSecret = $config->FB_clientSecret;
            }
            
            if (isset($config->FB_redirectUri)) {
                $redirectUri = $config->FB_redirectUri;
            }
            if (isset($config->FB_scope)) {
                $scope = $config->scope;
            }else{
                $scope = ['id', 'first_name', 'last_name', 'email', 'gender', 'locale', 'picture'];
            }
            $config_file = true;
        }
            if (empty($clientID) || empty($clientSecret) || empty($redirectUri)) {
                die('Facebook Api not config. Go to <a href="https://developers.facebook.com/docs/facebook-login/web/accesstokens">https://developers.facebook.com/docs/facebook-login/web/accesstokens</a> to information');
            }
            $this->client = new \Facebook\Facebook([
                'app_id'=>$clientID,
                'app_secret'=>$clientSecret,
                'default_graph_version'=>'v2.10'
            ]);
            $this->helper  = $this->client->getRedirectLoginHelper();
            $this->facebook_login_url = $this->helper->getLoginUrl($redirectUri, $scope);
    }

    public function urlLogin() {
        return $this->facebook_login_url;
    }

    public function decode($callback, $args = []) {
        $error = '';
        try {
            $accessToken = $this->facebook_helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {  
            $error = $e->getMessage();  
        } catch(Facebook\Exceptions\FacebookSDKException $e) {  
            $error = $e->getMessage();  
        } 
        if (!isset($accessToken)) {
            return false;
        }
        $oAuth2Client = $this->client->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        try {
            $user = $this->client->get('/me?fields='. implode($this->scope), $accessToken);
            return \call_user_func($callback, $user, $args);
        } catch(Exception $e) {
            return false;
        }
    }
}