<?php

require_once('Google/autoload.php');

class Google {

    protected $CI;

    public function __construct() {

        $config['google_client_id'] = env('google.client_id');
        $config['google_client_secret'] = env('google.client_secret');
        $google_login_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        if (strpos(explode("?", $google_login_url)[0], "googlelogin")) {
            $config['google_redirect_url'] = base_url() . 'pub/googleAuth/googlelogin';
        } else if (strpos(explode("?", $google_login_url)[0], "googleconnect")) {
            $config['google_redirect_url'] = base_url() . 'pub/googleAuth/googleconnect';
        } else {
            $config['google_redirect_url'] = base_url() . 'pub/googleAuth/oauth2callback';
        }
        $this->client = new Google_Client();
        $this->client->setClientId($config['google_client_id']);
        $this->client->setClientSecret($config['google_client_secret']);
        $this->client->setRedirectUri($config['google_redirect_url']);
        $this->client->setScopes(array(
            "https://www.googleapis.com/auth/plus.login",
            "https://www.googleapis.com/auth/plus.me",
            "https://www.googleapis.com/auth/userinfo.email",
            "https://www.googleapis.com/auth/userinfo.profile"
                )
        );
    }

    public function get_login_url() {
        return $this->client->createAuthUrl();
    }

    public function validate() {
        if (isset($_GET['code'])) {
            $this->client->authenticate($_GET['code']);
            $_SESSION['access_token'] = $this->client->getAccessToken();
        }
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            $this->client->setAccessToken($_SESSION['access_token']);
            $plus = new Google_Service_Plus($this->client);
            $person = $plus->people->get('me');
            $info['id'] = $person['id'];
            $info['email_address'] = $person['emails'][0]['value'];
            $info['displayName'] = $person['displayName'];
            return $info;
        }
    }

}
