<?php

namespace wechat\models;

use Yii;

/**
 * This is the model class for table "wechat_config".
 *
 * @property integer $id
 * @property string $appid
 * @property string $secret
 * @property string $access_token
 */
class WechatConfig extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'wechat_config';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['appid', 'secret', 'access_token'], 'required'],
            [['appid', 'secret'], 'string', 'max' => 64],
            [['access_token'], 'string', 'max' => 512],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appid' => 'Appid',
            'secret' => 'Secret',
            'access_token' => 'Access Token',
        ];
    }

    public function getConfig()
    {
        return static::findOne(['id' => 1]);
    }

    public function setConfig($token)
    {
        if ($this->validate()) {
            $this->token = $token;
            return $this->save();
        } else {
            return false;
        }
    }
}
