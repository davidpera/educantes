<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UsuariosSearch represents the model behind the search form of `app\models\Usuarios`.
 */
class UsuariosSearch extends Usuarios
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'nif', 'direccion', 'email', 'rol'], 'safe'],
            [['tel_movil'], 'number'],
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
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol === 'C') {
            $query = Usuarios::find()->andWhere(['colegio_id' => $us->colegio_id])
            ->andWhere(['or',
               ['rol' => 'V'],
               ['rol' => 'P'],
            ]);
        } else {
            $query = Usuarios::find();
        }

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
            'tel_movil' => $this->tel_movil,
        ]);

        $query->andFilterWhere(['ilike', 'nom_usuario', $this->nom_usuario])
            ->andFilterWhere(['ilike', 'password', $this->password])
            ->andFilterWhere(['ilike', 'nombre', $this->nombre])
            ->andFilterWhere(['ilike', 'apellidos', $this->apellidos])
            ->andFilterWhere(['ilike', 'nif', $this->nif])
            ->andFilterWhere(['ilike', 'direccion', $this->direccion])
            ->andFilterWhere(['ilike', 'email', $this->email])
            ->andFilterWhere(['ilike', 'rol', $this->rol]);

        return $dataProvider;
    }
}
