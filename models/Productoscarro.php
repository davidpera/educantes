<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "productoscarro".
 *
 * @property int $id
 * @property int $carro_id
 * @property int $uniforme_id
 * @property string $cantidad
 *
 * @property Carros $carro
 * @property Uniformes $uniforme
 */
class Productoscarro extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'productoscarro';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carro_id', 'uniforme_id', 'cantidad'], 'required'],
            [['carro_id', 'uniforme_id'], 'default', 'value' => null],
            [['carro_id', 'uniforme_id'], 'integer'],
            [['cantidad'], 'number'],
            [['carro_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carros::className(), 'targetAttribute' => ['carro_id' => 'id']],
            [['uniforme_id'], 'exist', 'skipOnError' => true, 'targetClass' => Uniformes::className(), 'targetAttribute' => ['uniforme_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'carro_id' => 'Carro ID',
            'uniforme_id' => 'Uniforme ID',
            'cantidad' => 'Cantidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCarro()
    {
        return $this->hasOne(Carros::className(), ['id' => 'carro_id'])->inverseOf('productoscarros');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUniforme()
    {
        return $this->hasOne(Uniformes::className(), ['id' => 'uniforme_id'])->inverseOf('productoscarros');
    }
}
