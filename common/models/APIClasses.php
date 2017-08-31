<?php
namespace common\models;

use Yii;

class APIClasses
{
    public function get ($url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
            'X-Pear-Token: '.(isset($_COOKIE['token']) ? $_COOKIE['token'] : '')
            ]
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
    
    public function post ($url, $data) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result);
    }
    
    public function postjson ($url, $data) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
            'X-Pear-Token: '.(isset($_COOKIE['token']) ? $_COOKIE['token'] : ''),
            'Content-Type: application/json',
            'Content-Length: '.strlen($data)
            ],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return json_decode($result);
    }

    public function postSSL ($url, $data) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSLCERT => getcwd().'/cert/apiclient_cert.pem',
            CURLOPT_SSLKEY => getcwd().'/cert/apiclient_key.pem',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data
        ]);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function curl($type, $url, $param = [], $data = '', $header = []) {
        if (sizeof($param)) {
            $temp = '';
            foreach ($param as $key => $value) {
                $temp .= "&$key=$value";
            }
            $url .= '?'.substr($temp, 1);
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 10
        ]);
        if ($type ===  'post') {
            curl_setopt_array($ch, [
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $data
            ]);
        }
        if (sizeof($header)) {
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => $header
            ]);
        }
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return [
            'info' => $info,
            'result' => $result
        ];
    }
}

