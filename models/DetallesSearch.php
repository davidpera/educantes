<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Detalles;

/**
 * DetallesSearch represents the model behind the search form of `app\models\Detalles`.
 */
class DetallesSearch extends Detalles
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['num_detalle', 'factura_id', 'uniformes_id'], 'integer'],
            [['cantidad'], 'number'],
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
        $query = Detalles::find();

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
            'num_detalle' => $this->num_detalle,
            'factura_id' => $this->factura_id,
            'uniformes_id' => $this->uniformes_id,
            'cantidad' => $this->cantidad,
        ]);

        return $dataProvider;
    }
}
