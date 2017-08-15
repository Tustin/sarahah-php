<?php

include_once dirname(__FILE__) . '/agent.php';

class Sarahah extends Agent {

    public $auth_token = "";
    public $refresh_token = "";

    public $username= "";
    public $password = "";

    //TODO: Allow passing of either token to login
    public function __construct($username, $password) {
        $this->username = $username;
        $this->password = $password;

    }

    public function login() {
        $result = parent::post('account/login', [
            "grant_type" => "password",
            "password" =>   $this->password,
            "scope" =>      "offline_access",
            "username" =>   $this->username
        ]);
        
        return $result;
    }
}
