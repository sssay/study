<?php
namespace common\models;

use Yii;
use yii\base\Model;

class Node extends Model
{
    public $userid;
    public $server;
    public $api;

    public function __construct()
    {
        $this->userid = isset($_SESSION['__id']) ? $_SESSION['__id'] : '';
        // $this->server = 'http://192.168.1.130:7200/v1/vdn';
        $this->server = 'https://api.webrtc.win:7201/v1/vdn';
        $this->api = [
            'signup' => [
                'url' => "$this->server/owner/signup",
                'type' => "post"
            ],
            'login' => [
                'url' => "$this->server/owner/login",
                'type' => "post"
            ],
            'logout' => [
                'url' => "$this->server/owner/$this->userid/logout",
                'type' => "post"
            ],
            'getOwnerInfo' => [
                'url' => "$this->server/owner/$this->userid",
                'type' => "get"
            ],
            'getNodeInfo' => [
                'url' => "$this->server/owner/$this->userid/nodes",
                'type' => "get"
            ],
            'getCacheFiles' => [
                'url' => "$this->server/owner/$this->userid/caches",
                'type' => "get"
            ],
            'updatePassword' => [
                'url' => "$this->server/owner/$this->userid/password",
                'type' => "post"
            ],
            'updateEmail' => [
                'url' => "$this->server/owner/$this->userid/email",
                'type' => "post"
            ],
            'updatePhone' => [
                'url' => "$this->server/owner/$this->userid/phone",
                'type' => "post"
            ],
            'bindNode' => [
                'url' => "$this->server/owner/$this->userid/node",
                'type' => "post"
            ],
            'resetPsw' => [
                'url' => "$this->server/owner/forget_password",
                'type' => "get"
            ],
        ];
    }

    public function curl($api, $data = '', $param = [])
    {
        $curl = new APIClasses();
        $type = $api['type'];
        $url = $api['url'];
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

    public function getOwnerInfo()
    {
        $api = $this->api['getOwnerInfo'];
        $response = $this->curl($api);
        $result = $response['result'];
        return [
            'user_name' => $result->user_name,
            'phone' => $result->phone,
            'email' => $result->email,
            'pear_coin' => $result->pear_coin,
        ];
    }

    public function getOwnerNodes()
    {
        $api = $this->api['getOwnerInfo'];
        $response = $this->curl($api);
        $result = $response['result'];
        return [
            'nodes' => $result->nodes,
        ];
    }

    public function getNodeInfo()
    {
        $api = $this->api['getNodeInfo'];
        $response = $this->curl($api);
        $result = $response['result'];
        $nodes = [];
        foreach ($result as $key => $value) {
            if ($value->mac_addr === $_GET['n']) {
                $nodes = $value;
            }
        }
        return [
            'nodes' => $nodes
        ];
    }

    public function getCacheFiles()
    {
        $api = $this->api['getCacheFiles'];
        $response = $this->curl($api);
        $result = $response['result'];
        $cache = [];
        foreach ($result as $key => $value) {
            if ($value->mac_addr === $_GET['n']) {
                $cache = $value->videos;
            }
        }
        return [
            'cache' => $cache
        ];
    }

    public function getBandwidth()
    {
        return [
            'userid' => $this->userid
        ];
    }

    public function getTraffic()
    {
        return [
            'userid' => $this->userid,
            'pear_coin' => $this->getOwnerInfo()['pear_coin'],
            'rate_tocoin' => 10,
            'rate_tormb' => 0.05
        ];
    }

    public function signup($data = [])
    {
        $api = $this->api['signup'];
        $data = json_encode([
            'user_name' => $data['user_name'],
            'password' => $data['nms_pwd'],
            'email' => $data['email']
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function login($data = [])
    {
        $api = $this->api['login'];
        $data = json_encode([
            'user_name' => $data['user_name'],
            'password' => $data['nms_pwd']
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

    public function updatePassword($data = [])
    {
        $api = $this->api['updatePassword'];
        $data = json_encode([
            'new_password' => $data['new_password'],
            'old_password' => $data['old_password'],
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function updateEmail($data = [])
    {
        $api = $this->api['updateEmail'];
        $data = json_encode([
            'email' => $data['email'],
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function updatePhone($data = [])
    {
        $api = $this->api['updatePhone'];
        $data = json_encode([
            'phone' => $data['phone_no'],
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function bindNode($data = [])
    {
        $api = $this->api['bindNode'];
        $data = json_encode([
            'mac_addr' => $data['mac_addr'],
            'sn' => $data['device_sn'],
            'platform' => 'UNKNOWN',
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function bindAccount($data = [])
    {
        $api = $this->api['bindAccount'];
        $data = json_encode([
            'state' => $data['state'],
            'openid' => $data['openid'],
            'username' => $data['username'],
            'password' => $data['password'],
        ]);
        $response = $this->curl($api, $data);
        return $response;
    }

    public function resetPsw($data = [])
    {
        $api = $this->api['resetPsw'];
        $data = array(
            'username' => $data['username'],
            'email' => $data['email'],
        );
        $response = $this->curl($api, '', $data);
        return $response;
    }
}
?>
