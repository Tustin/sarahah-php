<?php

abstract class Agent {

    const URL = "https://www.sarahah.com/api/";

    private static $CURL_OPTIONS = [
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'Sarahah/1.1.4 (com.sarahah; build:11; iOS 9.0.2) Alamofire/4.4.0',
        CURLOPT_HTTPHEADER => [
            'Cookie: .AspNetCore.Culture=c=en|uic=en', //??
        ]
    ];

    public static function subdomainExists($subdomain) {
        $res = self::get('account/subdomainexists?subdomain=' . $subdomain);
        if ($res === false) {
            return false;
        }
        return $res->status;
    }

    public static function emailExists($email) {
        $res = self::get('account/emailexists?email=' . $email);
        if ($res === false) {
            return false;
        }
        return $res->status;
    }

    public static function tokenValid($token) {
        $res = self::get('account/tokenvalid', $token);
        if ($res === false) {
            return false;
        }
        return $res->status;
    }

    public static function get($endpoint, $auth_token = null) {
        $ch = curl_init();

        $options = self::$CURL_OPTIONS + [
            CURLOPT_URL => self::URL . $endpoint,
        ];

        if ($auth_token != null) {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $auth_token;
        }

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        if ($result === false || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return json_decode($result);
    }


    public static function post($endpoint, $data, $json = false, $auth_token = null) {
        $ch = curl_init();

        //There are some inconsistencies with their API
        //Some requests are sent as JSON, while others are just normal post data
        $options = self::$CURL_OPTIONS + [
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $json ? json_encode($data) : http_build_query($data),
            CURLOPT_URL => self::URL . $endpoint,
        ];

        if ($auth_token != null) {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $auth_token;
        }

        if ($json) {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        if ($result === false || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        return json_decode($result);
    }

}