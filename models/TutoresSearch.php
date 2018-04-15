<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TutoresSearch represents the model behind the search form of `app\models\Tutores`.
 */
class TutoresSearch extends Tutores
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'colegio_id'], 'integer'],
            [['nif', 'nombre', 'apellidos', 'direccion', 'email'], 'safe'],
            [['telefono'], 'number'],
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
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Tutores::find()->where(['colegio_id' => Yii::$app->user->identity->colegio_id]);

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
            'telefono' => $this->telefono,
            'colegio_id' => $this->colegio_id,
        ]);

        $query->andFilterWhere(['ilike', 'nif', $this->nif])
            ->andFilterWhere(['ilike', 'nombre', $this->nombre])
            ->andFilterWhere(['ilike', 'apellidos', $this->apellidos])
            ->andFilterWhere(['ilike', 'direccion', $this->direccion])
            ->andFilterWhere(['ilike', 'email', $this->email]);

        return $dataProvider;
    }
}
