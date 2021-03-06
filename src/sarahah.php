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

        if ($this->login($username, $password) === false) {
            throw new Exception("Login failed");
        }
    }

    public function login($username, $password) {
        if (empty($username) || empty($password)) {
            return false;
        }

        $result = parent::post('account/login', [
            "grant_type" => "password",
            "password" =>   $password,
            "scope" =>      "offline_access",
            "username" =>   $username
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

    public static function searchUsers($query, $page = 0) {
        return Sarahah::get('account/search?name=' . $query . '&page=' . $page); //You don't need to be logged in to use this endpoint
    }

    public function createMessage($recipientId, $message) {
        return parent::post('message/create', [
            "recipientId" => $recipientId,
            "text" => $message
        ], true, $this->auth_token);
    }

    public function favoriteMessage($messageId, $favorite = "true") {
        return parent::put('message/favorite?messageID=' . $messageId . '&favorited=' . $favorite, $this->auth_token);
    }

    public static function sendPasswordReset($email) {
        return parent::get('account/forgotpassword?email=' . $email);
    }

    //Because Sarahah is all about anonymity, you have to block someone from one of their messages 
    public function blockUser($messageId) {
        $res = parent::put('message/block?messageID=' . $messageId, $this->auth_token);

        if ($res === false) {
            return false;
        }

        return $res->status;
    }

    public function reportMessage($messageId) {
        $res = parent::put('message/report?messageID=' . $messageId, $this->auth_token);

        if ($res === false) {
            return false;
        }

        return $res->status;
    }

    public function updateProfile($name, $email, $notifySounds, $appearInSearch, $notifyEmail, $notifyPush, $allowAnonymousMessages) {
        return parent::put('account/update', $this->auth_token, [
            "name" => $name,
            "email" => $email,
            "notifySounds" => $notifySounds,
            "appearInSearch" => $appearInSearch,
            "notifyEmail" => $notifyEmail,
            "notifyPush" => $notifyPush,
            "allowAnonymousMessages" => $allowAnonymousMessages,
        ], true);
    }

}
