<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NodeInfo;

/**
 * NodeInfoSearch represents the model behind the search form about `common\models\NodeInfo`.
 */
class NodeInfoSearch extends NodeInfo
{
    public $layout="adminIndex";
    public $icmp;
    public $tcp;
    public $http;
    public $https;
    public $time;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'traffic', 'last_heartbeat_time', 'cpu_cores', 'memory_free', 'storage_total', 'storage_used', 'storage_avail', 'upnp', 'pmp'], 'integer'],
            [['mac_addr', 'platform', 'public_ip', 'local_ip', 'remote_ip', 'version','icmp','tcp','http','time','https'], 'safe'],
            [['ping', 'upload_bw', 'download_bw'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // $query = NodeInfo::find();
        // $query->joinWith(['nodeprobe']);
        $query = NodeInfo::find();
        $query->joinWith(['nodeProbe']);
        $query->select("node_info.*, node_probe.icmp,node_probe.tcp, node_probe.http, node_probe.https, node_probe.time");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'node_info.id' => $this->id,
            'traffic' => $this->traffic,
            'last_heartbeat_time' => $this->last_heartbeat_time,
            'ping' => $this->ping,
            'upload_bw' => $this->upload_bw,
            'download_bw' => $this->download_bw,
            'cpu_cores' => $this->cpu_cores,
            'memory_free' => $this->memory_free,
            'storage_total' => $this->storage_total,
            'storage_used' => $this->storage_used,
            'storage_avail' => $this->storage_avail,
            'upnp' => $this->upnp,
            'pmp' => $this->pmp,
            'node_probe.icmp' => $this->icmp,
            'node_probe.tcp' => $this->tcp,
            'node_probe.http' => $this->http,
            'node_probe.https' => $this->https,
            'node_probe.time' => $this->time,
        ]);

        $query->andFilterWhere(['like', 'node_info.mac_addr', $this->mac_addr])
            ->andFilterWhere(['like', 'platform', $this->platform])
            ->andFilterWhere(['like', 'node_info.public_ip', $this->public_ip])
            ->andFilterWhere(['like', 'local_ip', $this->local_ip])
            ->andFilterWhere(['like', 'remote_ip', $this->remote_ip])
            ->andFilterWhere(['like', 'version', $this->version]);

        return $dataProvider;
    }
}
