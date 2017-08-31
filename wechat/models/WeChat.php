<?php

namespace wechat\models;

use Yii;
use yii\base\Model;
use common\models\APIClasses;

use frontend\models\NodeOwner;

class Wechat extends Model
{
    private $appid;
    private $secret;
    private $access_token;

    private $api;

    private $openid;
    private $originid;
    private $mch_id;
    private $api_key;

    public function __construct()
    {
        $db = new WechatConfig();
        $this->appid = $db->getConfig()->appid;
        $this->secret = $db->getConfig()->secret;
        $this->access_token = $db->getConfig()->access_token;

        $this->originid = $db->getConfig()->originid;
        $this->mch_id = $db->getConfig()->mch_id;
        $this->api_key = $db->getConfig()->api_key;

        $this->api = [
            'getToken' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/token',
                'type' => 'get'
            ],
            'getIPs' => [
                'url' => 'https://api.weixin.qq.com/cgi-bin/getcallbackip',
                'type' => 'get'
            ],
            'getAuth' => [
                'url' => 'https://api.weixin.qq.com/sns/oauth2/access_token',
                'type' => 'get'
            ],
            'withDraw' => [
                'url' => 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack',
                'type' => 'post'
            ],
            'getCoin' => [
                'url' => 'https://api.webrtc.win:7201/v1/vdn/owner/pear_coin/',
                'type' => 'get'
            ],
            'getRate' => [
                'url' => 'https://api.webrtc.win:7201/v1/vdn/owner/pear_coin_option/',
                'type' => 'get'
            ],
            'clearCoin' => [
                'url' => 'https://api.webrtc.win:7201/v1/vdn/owner/payment/',
                'type' => 'post'
            ],
        ];
    }

    public function curl($api, $data = '', $param = [])
    {
        $curl = new APIClasses();
        $type = $api['type'];
        $url = $api['url'];
        $header = [];
        $response = $curl->curl($type, $url, $param, $data, $header);
        $code = $response['info']['http_code'];
        $result = json_decode($response['result']);
        return [
            "code" => $code,
            "result" => $result
        ];
    }

    public function renderBindAccount($code)
    {
        if (!isset($_COOKIE['openid'])) {
            $api = $this->api["getAuth"];
            $param = [
                'grant_type' => 'authorization_code',
                'appid' => $this->appid,
                'secret' => $this->secret,
                'code' => $code
            ];
            $response = $this->curl($api, '', $param);
            $result = $response['result'];
            if (isset($result->openid)) {
                setcookie('openid', $result->openid, time() + $result->expires_in);
            } else {
                setcookie('openid', 'error', time() + 7200);
            }
        }
        return $_COOKIE['openid'];
    }

    public function checkSignature($data)
    {
        $token = 'pear';
        $signature = isset($data['signature']) ? $data['signature'] : '';
        $timestamp = isset($data['timestamp']) ? $data['timestamp'] : '';
        $nonce = isset($data['nonce']) ? $data['nonce'] : '';
        $echostr = isset($data['echostr']) ? $data['echostr'] : '';

        $array = [$token, $timestamp, $nonce];
        sort($array, SORT_STRING);
        $str = sha1(implode($array));
        if ($signature == $str) {
            return $echostr;
        }
    }

    public function available()
    {
        $curl = new APIClasses;
        $db = new WechatConfig;

        $test = $curl->get($this->api['getIPs']['url'].
            "?access_token=".$this->access_token
        );
        if (isset($test->errcode)) {
            $result = $curl->get($this->api['getToken'].
                "?grant_type=client_credential".
                "&appid=".$this->appid.
                "&secret=".$this->secret
            );
            return $db->setConfig($result->access_token);
        }
    }

    public function log($input)
    {
        $fd = fopen('log/log.txt', 'a');
        fwrite($fd, $input.'\n');
        fclose($fd);
    }

    /*****************************************************
    *   Massage Start
    *****************************************************/

    public function XMLExtract($xml)
    {
        $parser = xml_parser_create();
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parse_into_struct($parser, $xml, $temp);
        $array = [];
        foreach ($temp as $key => $value) {
            if ($value['type'] === 'complete') {
                $array[$value['tag']] = $value['value'];
            }
        }
        return $array;
    }

    public function XMLGenerate($data)
    {
        $xml = "";
        foreach ($data as $key => $value) {
            if ($key === 'CreateTime') {
                $xml .= "<$key>$value</$key>";
            } else {
                $xml .= "<$key><![CDATA[$value]]></$key>";
            }
        }
        return $xml = "<xml>$xml</xml>";
    }

    public function sendMsg($template, $content)
    {
        switch ($template) {
            case 'text':
                $xml = $this->XMLGenerate([
                    'ToUserName' => $this->openid,
                    'FromUserName' => $this->originid,
                    'CreateTime' => time(),
                    'MsgType' => 'text',
                    'Content' => $content,
                ]);
                break;
        }
        return $xml;
    }

    public function onClick($eventKey)
    {
        switch ($eventKey) {
            case 'V1001_ACCOUNT_SEARCH':
                $account = $this->checkAccount();
                $content = '您已绑定PearVDN帐号，用户名为'.$account;
                break;
            // case 'V2002_DEVICE_SEARCH':
            //     break;
            case 'V3001_COIN_SEARCH':
                $coin = $this->getCoin();
                $content = '您的Pear Coin余额为'.$coin;
                break;
            case 'V3002_COIN_RATE':
                $rate = $this->getRate();
                $content = '当前汇率为1Pear Coin兑换'.$rate.'元';
                break;
            case 'V3003_COIN_WITHDRAW':
                if($this->getCoin() >= 20) {
                    $result = $this->withDraw();
                    if (!isset($this->XMLExtract($result)['err_code'])) {
                        $content = $this->XMLExtract($result)['return_msg'];
                        $this->clearCoin();
                    } else {
                        $content = '提现失败，请稍候再试';
                    }
                    if($this->openid === "omOX00x6VVoeitv82QTf8idQtTqY") {
                        $content = $this->XMLExtract($result)['return_msg'];
                    }
                } else {
                    $content = 'Pear Coin必须大于20才可提现';
                }
                break;
            default:
                $content = $eventKey;
                break;
        }
        return $content;
    }

    public function onEvent($event, $eventKey)
    {
        switch ($event) {
            case 'subscribe':
                $content = '欢迎关注梨享雾计算～';
                break;
            case 'CLICK':
                $account = $this->checkAccount();
                if (!$account) {
                    $content = '您暂未绑定PearVDN帐号，立即前往“帐号”-“绑定帐号”';
                } else {
                    $content = $this->onClick($eventKey);
                }
                break;
            default:
                $content = $event;
                break;
        }
        return $content;
    }

    public function msg($xml)
    {
        $msg = $this->XMLExtract($xml);
        $this->openid = $msg['FromUserName'];
        switch ($msg['MsgType']) {
            case 'event':
                $content = $this->onEvent($msg['Event'], $msg['EventKey']);
                $result = $this->sendMsg('text', $content);
                break;
            default:
                break;
        }
        return $result;
    }

    /*****************************************************
    *   Massage Stop
    *****************************************************/

    /*****************************************************
    *   Coin Start
    *****************************************************/

    public function getRandomString($length)
    {
        $lib = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= $lib[random_int(0, strlen($lib) - 1)];
        }
        return $random;
    }

    public function getSign($data)
    {
        $strA = "";
        foreach ($data as $key => $value) {
            if ($value) {
                $strA .= "&$key=$value";
            }
        }
        $strA = substr($strA, 1)."&key=".$this->api_key;
        $sign = strtoupper(md5($strA));
        return $sign;
    }

    public function checkAccount()
    {
        $data = NodeOwner::find()->select(['user_name'])->where(['wechat_acct' => $this->openid])->one();
        if ($data) {
            return $data->user_name;
        } else {
            return false;
        }
    }

    public function getCoin()
    {
        $curl = new APIClasses;
        $result = $curl->get($this->api["getCoin"]["url"]
                    .$this->openid
                );
        $coin = isset($result->pear_coin) ? $result->pear_coin : 0;
        return $coin;
    }

    public function getRate()
    {
        $curl = new APIClasses;
        $result = $curl->get($this->api["getRate"]["url"]
                    .$this->openid
                );
        $rate = isset($result->coin_to_cash) ? $result->coin_to_cash : 0;
        return $rate;
    }

    public function clearCoin()
    {
        $curl = new APIClasses;
        $result = $curl->post($this->api["clearCoin"]["url"]
                    .$this->openid, ""
                );
        return true;
    }

    public function withDraw()
    {
        $curl = new APIClasses();
        $coin = $this->getCoin();
        $rate = $this->getRate();
        $rmb = 100 * $coin * $rate;
        $data = [
            'act_name' => '提现红包',
            'client_ip' => '127.0.0.1',
            'mch_billno' => $this->mch_id.date('YmdHis'),
            'mch_id' => $this->mch_id,
            'nonce_str' => $this->getRandomString(32),
            're_openid' => $this->openid,
            // 're_openid' => 'omOX00x6VVoeitv82QTf8idQtTqY',
            'remark' => '红包!红包!红包',
            // 'scene_id' => 'PRODUCT_3',
            'send_name' => 'PearVDN',
            'total_amount' => $rmb,
            'total_num' => 1,
            'wishing' => 'BestWish',
            'wxappid' => $this->appid,
        ];
        $data['sign'] = $this->getSign($data);
        $result = $curl->postSSL($this->api["withDraw"]["url"],
            $this->XMLGenerate($data)
        );
        return $result;
    }

    /*****************************************************
    *   Coin Stop
    *****************************************************/
}
