<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UniformesSearch represents the model behind the search form of `app\models\Uniformes`.
 */
class UniformesSearch extends Uniformes
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['codigo', 'descripcion', 'talla', 'ubicacion', 'colegio.nombre'], 'safe'],
            [['precio', 'iva', 'cantidad'], 'number'],
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
     * @param null|mixed $mio
     *
     * @return ActiveDataProvider
     */
    public function search($params, $mio)
    {
        if ($mio !== 'no') {
            $query = Uniformes::find()->where(['colegio_id' => Yii::$app->user->identity->colegio_id])->joinWith(['colegio']);
        } else {
            $query = Uniformes::find()->where(['!=', 'colegio_id', Yii::$app->user->identity->colegio_id])->joinWith(['colegio']);
        }

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['descripcion' => SORT_ASC]],
        ]);
        if (Yii::$app->user->identity->rol === 'P') {
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'sort' => ['defaultOrder' => ['descripcion' => SORT_ASC]],
                'pagination' => false,
            ]);
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['colegio.nombre'] = [
            'asc' => ['colegios.nombre' => SORT_ASC],
            'desc' => ['colegios.nombre' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'precio' => $this->precio,
            'iva' => $this->iva,
            'cantidad' => $this->cantidad,
        ]);

        $query->andFilterWhere(['ilike', 'codigo', $this->codigo])
            ->andFilterWhere(['ilike', 'descripcion', $this->descripcion])
            ->andFilterWhere(['ilike', 'talla', $this->talla])
            ->andFilterWhere(['ilike', 'ubicacion', $this->ubicacion])
            ->andFilterWhere(['ilike', 'colegios.nombre', $this->getAttribute('colegio.nombre')]);

        return $dataProvider;
    }
}
