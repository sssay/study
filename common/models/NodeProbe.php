<?php

namespace common\models;

use Yii;

use frontend\models\NodeOwner;
use frontend\models\NodeInfo;

/**
 * This is the model class for table "node_probe".
 *
 * @property integer $id
 * @property string $mac_addr
 * @property string $public_ip
 * @property string $icmp
 * @property string $tcp
 * @property string $http
 * @property string $https
 * @property integer $time
 */
class NodeProbe extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'node_probe';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mac_addr', 'public_ip'], 'required'],
            [['time'], 'integer'],
            [['mac_addr', 'public_ip', 'http', 'https'], 'string', 'max' => 32],
            [['icmp', 'tcp'], 'string', 'max' => 16],
            [['mac_addr'], 'unique'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'mac_addr' => 'Mac Addr',
            'public_ip' => 'Public Ip',
            'icmp' => 'Icmp',
            'tcp' => 'Tcp',
            'http' => 'Http',
            'https' => 'Https',
            'time' => 'Time',
        ];
    }

    /**
     * @inheritdoc
     * @return NodeProbeQuery the active query used by this AR class.
     */
    // public static function find()
    // {
    //     return new NodeProbeQuery(get_called_class());
    // }
}
