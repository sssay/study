<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "node_info".
 *
 * @property integer $id
 * @property string $mac_addr
 * @property string $platform
 * @property string $public_ip
 * @property string $local_ip
 * @property string $remote_ip
 * @property integer $traffic
 * @property integer $last_heartbeat_time
 * @property double $ping
 * @property double $upload_bw
 * @property double $download_bw
 * @property string $version
 * @property integer $cpu_cores
 * @property integer $memory_free
 * @property integer $storage_total
 * @property integer $storage_used
 * @property integer $storage_avail
 * @property integer $upnp
 * @property integer $pmp
 */
class NodeInfo extends ActiveRecord 
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'node_info';
    }

    public function getNodeProbe()
    {
         return $this->hasOne(NodeProbe::className(), ['public_ip' => 'public_ip']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mac_addr', 'platform', 'public_ip', 'local_ip', 'remote_ip', 'traffic', 'last_heartbeat_time', 'icmp', 'upload_bw', 'download_bw', 'version', 'cpu_cores', 'memory_free', 'storage_total', 'storage_used', 'storage_avail', 'upnp', 'pmp'], 'required'],
            [['traffic', 'last_heartbeat_time', 'cpu_cores', 'memory_free', 'storage_total', 'storage_used', 'storage_avail', 'upnp', 'pmp'], 'integer'],
            [['icmp', 'upload_bw', 'download_bw'], 'number'],
            [['mac_addr', 'public_ip', 'local_ip', 'remote_ip'], 'string', 'max' => 24],
            [['platform'], 'string', 'max' => 32],
            [['version'], 'string', 'max' => 8],
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
            'platform' => 'Platform',
            'public_ip' => 'Public Ip',
            'local_ip' => 'Local Ip',
            'remote_ip' => 'Remote Ip',
            'traffic' => 'Traffic',
            'last_heartbeat_time' => 'Last Heartbeat Time',
            'icmp' => 'Icmp',
            'upload_bw' => 'Upload Bw',
            'download_bw' => 'Download Bw',
            'version' => 'Version',
            'cpu_cores' => 'Cpu Cores',
            'memory_free' => 'Memory Free',
            'storage_total' => 'Storage Total',
            'storage_used' => 'Storage Used',
            'storage_avail' => 'Storage Avail',
            'upnp' => 'Upnp',
            'pmp' => 'Pmp',
        ];
    }
}
