<?php

namespace app\models;

use yii\helpers\Url;
use yii\imagine\Image;

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
    public $foto;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'uniformes';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'foto',
        ]);
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
            [['foto'], 'file', 'extensions' => 'jpg'],
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
            'colegio.nombre' => 'Colegio',
            'secstock.mp' => 'Cantidad cuando pedir',
        ];
    }

    public function getRutaImagen()
    {
        $nombre = 'uploads/' . $this->codigo . '.jpg';
        // var_dump($this->codigo);
        // die();
        if (file_exists($nombre)) {
            return Url::to('/uploads/') . $this->codigo . '.jpg';
        }
        return Url::to('/uploads/') . 'default.jpg';
    }

    public function upload()
    {
        if ($this->foto === null) {
            return true;
        }
        $nombre = 'uploads/' . $this->codigo . '.jpg';
        // var_dump($this->codigo);
        // die();
        $res = $this->foto->saveAs($nombre);
        if ($res) {
            Image::thumbnail($nombre, 150, null)->save($nombre);
        }
        return $res;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSecstock()
    {
        if (Secstocks::find()->where(['uniforme_id' => $this->id])->count('*') !== 0) {
            return $this->hasOne(Secstocks::className(), ['uniforme_id' => 'id'])->inverseOf('uniforme');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColegio()
    {
        return $this->hasOne(Colegios::className(), ['id' => 'colegio_id'])->inverseOf('uniformes');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProductoscarros()
    {
        return $this->hasMany(Productoscarro::className(), ['uniforme_id' => 'id'])->inverseOf('uniforme');
    }
}
