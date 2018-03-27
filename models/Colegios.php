<?php

namespace app\models;

/**
 * This is the model class for table "colegios".
 *
 * @property int $id
 * @property string $cif
 * @property string $nombre
 * @property string $email
 * @property string $cod_postal
 * @property string $direccion
 *
 * @property Alumnos[] $alumnos
 * @property Correos[] $correos
 * @property Libros[] $libros
 * @property Sms[] $sms
 * @property Usuarios[] $usuarios
 */
class Colegios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'colegios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cif', 'nombre', 'email', 'cod_postal', 'direccion'], 'required'],
            [['cod_postal'], 'number'],
            [['cif'], 'string', 'max' => 9],
            [['nombre', 'email', 'direccion'], 'string', 'max' => 255],
            [['cif'], 'unique'],
            [['email'], 'unique'],
            [['nombre'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cif' => 'Cif',
            'nombre' => 'Colegio',
            'email' => 'Email',
            'cod_postal' => 'Cod Postal',
            'direccion' => 'Direccion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos()
    {
        return $this->hasMany(Alumnos::className(), ['colegio_id' => 'id'])->inverseOf('colegio');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorreos()
    {
        return $this->hasMany(Correos::className(), ['emisario_id' => 'id'])->inverseOf('emisario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLibros()
    {
        return $this->hasMany(Libros::className(), ['colegio_id' => 'id'])->inverseOf('colegio');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms()
    {
        return $this->hasMany(Sms::className(), ['emisario_id' => 'id'])->inverseOf('emisario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios()
    {
        return $this->hasMany(Usuarios::className(), ['colegio_id' => 'id'])->inverseOf('colegio');
    }

    public function getUniformes()
    {
        return $this->hasMany(Uniformes::className(), ['colegio_id' => 'id'])->inverseOf('colegio');
    }
}
