<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nom_usuario
 * @property string $password
 * @property string $nombre
 * @property string $apellidos
 * @property string $nif
 * @property string $direccion
 * @property string $email
 * @property string $tel_movil
 * @property string $rol
 *
 * @property Correos[] $correos
 * @property Facturas[] $facturas
 * @property Sms[] $sms
 */
class Usuarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'nif', 'email', 'tel_movil', 'rol'], 'required'],
            [['tel_movil'], 'number'],
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'direccion', 'email'], 'string', 'max' => 255],
            [['nif'], 'string', 'max' => 10],
            [['rol'], 'string', 'max' => 1],
            [['email'], 'unique'],
            [['nif'], 'unique'],
            [['nom_usuario'], 'unique'],
            [['tel_movil'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nom_usuario' => 'Nom Usuario',
            'password' => 'Password',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'nif' => 'Nif',
            'direccion' => 'Direccion',
            'email' => 'Email',
            'tel_movil' => 'Tel Movil',
            'rol' => 'Rol',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorreos()
    {
        return $this->hasMany(Correos::className(), ['receptor_id' => 'id'])->inverseOf('receptor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Facturas::className(), ['usuario_id' => 'id'])->inverseOf('usuario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms()
    {
        return $this->hasMany(Sms::className(), ['receptor_id' => 'id'])->inverseOf('receptor');
    }
}
