<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "carros".
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $productos
 *
 * @property Usuarios $usuario
 * @property Productoscarro[] $productoscarros
 */
class Carros extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carros';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id'], 'required'],
            [['usuario_id'], 'default', 'value' => null],
            [['usuario_id'], 'integer'],
            [['productos'], 'number'],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'productos' => 'Productos',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id'])->inverseOf('carros');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductoscarros()
    {
        return $this->hasMany(Productoscarro::className(), ['carro_id' => 'id'])->inverseOf('carro');
    }
}
