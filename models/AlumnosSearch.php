<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * AlumnosSearch represents the model behind the search form of `app\models\Alumnos`.
 */
class AlumnosSearch extends Alumnos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'colegio_id'], 'integer'],
            [['codigo', 'nombre', 'primer_apellido', 'segundo_apellido', 'fecha_de_nacimiento', 'dni_primer_tutor', 'dni_segundo_tutor', 'unidad'], 'safe'],
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
        $query = Alumnos::find()->where(['colegio_id' => Yii::$app->user->identity->colegio_id]);

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
            'fecha_de_nacimiento' => $this->fecha_de_nacimiento,
            'dni_primer_tutor' => $this->dni_primer_tutor,
            'dni_segundo_tutor' => $this->dni_segundo_tutor,
            'colegio_id' => $this->colegio_id,
        ]);

        $query->andFilterWhere(['ilike', 'codigo', $this->codigo])
            ->andFilterWhere(['ilike', 'nombre', $this->nombre])
            ->andFilterWhere(['ilike', 'primer_apellido', $this->primer_apellido])
            ->andFilterWhere(['ilike', 'segundo_apellido', $this->segundo_apellido])
            ->andFilterWhere(['ilike', 'unidad', $this->unidad]);

        return $dataProvider;
    }
}
