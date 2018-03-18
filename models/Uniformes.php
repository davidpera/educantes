<?php

namespace app\models;

/**
 * This is the model class for table "uniformes".
 *
 * @property int $id
 * @property string $codigo
 * @property string $descripcion
 * @property string $talla
 * @property string $precio
 * @property string $iva
 * @property string $ubicacion
 * @property string $cantidad
 * @property int $colegio_id
 *
 * @property Detalles[] $detalles
 * @property Secstocks[] $secstocks
 */
class Uniformes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uniformes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'descripcion', 'talla', 'precio', 'iva', 'cantidad', 'colegio_id'], 'required'],
            [['precio', 'iva', 'cantidad'], 'number'],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['codigo', 'descripcion', 'talla', 'ubicacion'], 'string', 'max' => 255],
            [['codigo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'codigo' => 'Codigo',
            'descripcion' => 'Descripcion',
            'talla' => 'Talla',
            'precio' => 'Precio',
            'iva' => 'Iva',
            'ubicacion' => 'Ubicacion',
            'cantidad' => 'Cantidad',
            'colegio_id' => 'Colegio ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDetalles()
    {
        return $this->hasMany(Detalles::className(), ['uniformes_id' => 'id'])->inverseOf('uniformes');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecstocks()
    {
        return $this->hasMany(Secstocks::className(), ['uniforme_id' => 'id'])->inverseOf('uniforme');
    }

    public function getColegio()
    {
        if ($this->colegio_id !== null) {
            return $this->hasOne(Colegios::className(), ['id' => 'colegio_id'])->inverseOf('usuarios');
        }
    }
}
