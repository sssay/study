<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NodeProbe;

/**
 * NodeProbeSearch represents the model behind the search form about `common\models\NodeProbe`.
 */
class NodeProbeSearch extends NodeProbe
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'time'], 'integer'],
            [['mac_addr', 'public_ip', 'icmp', 'tcp', 'http', 'https'], 'safe'],
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
    // public function search($params)
    // {
    //     $query = NodeProbe::find();

    //     // add conditions that should always apply here

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //     ]);

    //     $this->load($params);

    //     if (!$this->validate()) {
    //         // uncomment the following line if you do not want to return any records when validation fails
    //         // $query->where('0=1');
    //         return $dataProvider;
    //     }

    //     // grid filtering conditions
    //     $query->andFilterWhere([
    //         'id' => $this->id,
    //         'time' => $this->time,
    //     ]);

    //     $query->andFilterWhere(['like', 'mac_addr', $this->mac_addr])
    //         ->andFilterWhere(['like', 'public_ip', $this->public_ip])
    //         ->andFilterWhere(['like', 'icmp', $this->icmp])
    //         ->andFilterWhere(['like', 'tcp', $this->tcp])
    //         ->andFilterWhere(['like', 'http', $this->http])
    //         ->andFilterWhere(['like', 'https', $this->https]);

    //     return $dataProvider;
    // }
}
