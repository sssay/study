<?php
namespace common\models;

use Yii;
use yii\base\Model;
use common\models\APIClasses;

class Node extends Model
{
    public $userid;
    public $server;
    public $api;

    public function __construct()
    {
        $host_id = isset($_GET['host_id']) ? $_GET['host_id'] : '';
        $this->userid = isset($_SESSION['__id']) ? $_SESSION['__id'] : '';
        // $this->server = 'http://192.168.1.130:7200/v1/vdn';
        // $this->server = 'https://nms.webrtc.win:7201/v1/vdn';
        $this->server = 'https://api.webrtc.win:7401/v1/oss';
        $this->api = [
            'signup' => [
                'url' => "$this->server/cp/signup",
                'type' => "post"
            ],
            'login' => [
                'url' => "$this->server/cp/login",
                'type' => "post"
            ],
            'logout' => [
                'url' => "$this->server/cp/$this->userid/logout",
                'type' => "post"
            ],
            'addHost' => [
                'url' => "$this->server/cp/$this->userid/hosts",
                'type' => "post"
            ],
            'hosts' => [
                'url' => "$this->server/cp/$this->userid/hosts",
                'type' => "get"
            ],
            'delete' => [
                'url' => "$this->server/cp/$this->userid/hosts/$host_id",
                'type' => "post"
            ],
            'traffic' => [
                'url' => "$this->server/cp/$this->userid/traffic",
                'type' => "get"
            ],
            'cacheInfo' => [
                'url' => "$this->server/cp/$this->userid/cache/configs",
                'type' => "get"
            ],
            'cacheUpdate' => [
                'url' => "$this->server/cp/$this->userid/cache/configs",
                'type' => "post"
            ],
        ];
    }

    public function curl($api, $data = '', $param = [])
    {
        $curl = new APIClasses();
        $type = $api['type'];
        $url = $api['url'];
        $param = [];
        $header = [
            'X-Pear-Token: '.(isset($_COOKIE['token']) ? $_COOKIE['token'] : ''),
        ];
        if ($data) {
            array_push($header,
                'Content-Type: application/json',
                'Content-Length: '.strlen($data)
            );
        }
        $response = $curl->curl($type, $url, $param, $data, $header);
        $code = $response['info']['http_code'];
        $result = json_decode($response['result']);
        return [
            "code" => $code,
            "result" => $result
        ];
    }

    public function signup($data = [])
    {
        $api = $this->api['signup'];
        $data = json_encode([
            'user_name' => $data['user_name'],
            'password' => $data['password']
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function login($data = [])
    {
        $api = $this->api['login'];
        $data = json_encode([
            'user_name' => $data['user_name'],
            'password' => $data['password']
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function logout()
    {
        $api = $this->api['logout'];
        $response = $this->curl($api);
        $code = $response['code'];
        return $code;
    }

    public function addHost($data = [])
    {
        $api = $this->api['addHost'];
        $data = json_encode([
            'host' => $data['host'],
        ]);
        $response = $this->curl($api, $data);
        return $response;  
    }

    public function getHosts()
    {
        $api = $this->api['hosts'];
        $response = $this->curl($api);
        $result = $response['result'];
        return [
            'host' => $result,
            'userid' => $this->userid,
        ];
    }

    public function deleteHost()
    {
        $api = $this->api['delete'];
        $response = $this->curl($api);
        $code = $response['code'];
        return $code;
    }

    public function traffic($data)
    {
        $api = $this->api['traffic'];
        if (sizeof($data)) {
            $temp = '';
            foreach ($data as $key => $value) {
                $temp .= '&'.$key.'='.$value;
            }
            $url =$api['url'] . '?'.substr($temp, 1);
        }
        $modle = new APIClasses();
        $result = $modle->get($url);
        return $url;
    }

    public function cacheInfo()
    {
        $api = $this->api['cacheInfo'];
        $response = $this->curl($api);
        return $response;
    }

    public function cacheUpdate($data)
    {
        $api = $this->api['cacheUpdate'];
        $data = json_encode([
            "type" => $data['type'],
            "videos" => $data['videos'],
        ]);
        $response = $this->curl($api, $data);
        $code = $response['code'];
        return $response;
    }
}
?>
