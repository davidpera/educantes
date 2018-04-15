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
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'nif', 'direccion', 'email', 'rol', 'colegio.nombre'], 'safe'],
            [['tel_movil'], 'number'],
        ];
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'colegio.nombre',
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
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol === 'C') {
            $query = Usuarios::find()->andWhere(['colegio_id' => $us->colegio_id])
            ->andWhere(['or',
               ['rol' => 'V'],
               ['rol' => 'P'],
            ]);
        } else {
            $query = Usuarios::find()->joinWith(['colegio']);
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
            ->andFilterWhere(['ilike', 'usuarios.nombre', $this->nombre])
            ->andFilterWhere(['ilike', 'apellidos', $this->apellidos])
            ->andFilterWhere(['ilike', 'nif', $this->nif])
            ->andFilterWhere(['ilike', 'usuarios.direccion', $this->direccion])
            ->andFilterWhere(['ilike', 'usuarios.email', $this->email])
            ->andFilterWhere(['ilike', 'rol', $this->rol]);
        if ($us->rol === 'A') {
            $query->andFilterWhere(['ilike', 'colegios.nombre', $this->getAttribute('colegio.nombre')]);
            $dataProvider->sort->attributes['colegio.nombre'] = [
                'asc' => ['colegios.nombre' => SORT_ASC],
                'desc' => ['colegios.nombre' => SORT_DESC],
            ];
        }

        return $dataProvider;
    }
}
