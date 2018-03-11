<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "detalles".
 *
 * @property int $num_detalle
 * @property int $factura_id
 * @property int $uniformes_id
 * @property string $cantidad
 *
 * @property Uniformes $uniformes
 */
class Detalles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'detalles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['factura_id', 'uniformes_id', 'cantidad'], 'required'],
            [['factura_id', 'uniformes_id'], 'default', 'value' => null],
            [['factura_id', 'uniformes_id'], 'integer'],
            [['cantidad'], 'number'],
            [['uniformes_id'], 'exist', 'skipOnError' => true, 'targetClass' => Uniformes::className(), 'targetAttribute' => ['uniformes_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'num_detalle' => 'Num Detalle',
            'factura_id' => 'Factura ID',
            'uniformes_id' => 'Uniformes ID',
            'cantidad' => 'Cantidad',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUniformes()
    {
        return $this->hasOne(Uniformes::className(), ['id' => 'uniformes_id'])->inverseOf('detalles');
    }
}
