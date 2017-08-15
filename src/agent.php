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

    public function get($endpoint, $auth_token = null) {
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


    public function post($endpoint, $data, $auth_token = null) {
        $ch = curl_init();

        $options = self::$CURL_OPTIONS + [
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($data),
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

}