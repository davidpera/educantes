<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Correos;

/**
 * CorreosSearch represents the model behind the search form of `app\models\Correos`.
 */
class CorreosSearch extends Correos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'emisario_id', 'receptor_id'], 'integer'],
            [['mensaje'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Correos::find();

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
            'id' => $this->id,
            'emisario_id' => $this->emisario_id,
            'receptor_id' => $this->receptor_id,
        ]);

        $query->andFilterWhere(['ilike', 'mensaje', $this->mensaje]);

        return $dataProvider;
    }
}
