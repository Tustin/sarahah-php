<?php

include_once dirname(__FILE__) . '/agent.php';

class Sarahah extends Agent {

    public $auth_token = "";
    public $refresh_token = "";
    public $expire_time = 0;

    public $username= "";
    public $password = "";

    //TODO: Allow passing of either token to login
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;

        $this->login();

    }

    public function login() {
        if (empty($this->username) || empty($this->password)) {
            return false;
        }

        $result = parent::post('account/login', [
            "grant_type" => "password",
            "password" =>   $this->password,
            "scope" =>      "offline_access",
            "username" =>   $this->username
        ]);

        if (!isset($result->access_token)) {
            return false;
        }

        $this->auth_token = $result->access_token;
        $this->refresh_token = $result->refresh_token;
        $this->expire_time = strtotime("+{$result->expires_in} seconds"); //expires_in is how many seconds until the token expires

        return $result;
    }

    public static function register($subdomain, $email, $password, $name, $photoURL = "") {
        //Why this needs to be done manually makes no sense to me
        if (Agent::subdomainExists($subdomain) || Agent::emailExists($email)) {
            return false;
        }

        $result = Agent::post('account/register', [
            "subdomain" => $subdomain,
            "photoURL" => $photoURL,
            "email" => $email,
            "name" => $name,
            "notifications" => true,
            "password" => $password          
        ], true);

        if ($result === false || (isset($result->status) && !$result->status)) {
            return false;
        }

        //Should be good to login now
        return new Sarahah($email, $password);
    }

    public function getProfile() {
        return parent::get('account/profile', $this->auth_token);
    }

    public function getSentMessages($page = 0) {
        return parent::get('message/sent?page='.$page, $this->auth_token);
    }

    public function getReceivedMessages($page = 0) {
        return parent::get('message/received?page='.$page, $this->auth_token);
    }

    public function getFavoritedMessages($page = 0) {
        return parent::get('message/favorited?page='.$page, $this->auth_token);
    }

    public function searchUsers($query, $page = 0) {
        return parent::get('account/search?name=' . $query . '&page=' . $page); //You don't need to be logged in to use this endpoint
    }

    public function createMessage($recipientId, $message) {
        return parent::post('message/create', [
            "recipientId" => $recipientId,
            "text" => $message
        ], true, $this->auth_token);
    }


}
