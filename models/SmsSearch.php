<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * SmsSearch represents the model behind the search form of `app\models\Sms`.
 */
class SmsSearch extends Sms
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'emisario_id', 'receptor_id'], 'integer'],
            [['mensaje', 'receptor.nombre', 'emisario.nombre'], 'safe'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'receptor.nombre',
            'emisario.nombre',
        ]);
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
        $query = Sms::find()->where(['receptor_id' => Yii::$app->user->id])->joinWith(['emisario'])->joinWith(['receptor']);

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

        $dataProvider->sort->attributes['emisario.nombre'] = [
            'asc' => ['usuarios.nombre' => SORT_ASC],
            'desc' => ['usuarios.nombre' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['receptor.nombre'] = [
            'asc' => ['usuarios.nombre' => SORT_ASC],
            'desc' => ['usuarios.nombre' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'emisario_id' => $this->emisario_id,
            'receptor_id' => $this->receptor_id,
        ]);

        $query->andFilterWhere(['ilike', 'mensaje', $this->mensaje])
            ->andFilterWhere(['ilike', 'usuarios.nombre', $this->getAttribute('emisario.nombre')])
            ->andFilterWhere(['ilike', 'usuarios.nombre', $this->getAttribute('receptor.nombre')]);

        return $dataProvider;
    }
}
