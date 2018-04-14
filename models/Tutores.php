<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tutores".
 *
 * @property int $id
 * @property string $nif
 * @property string $nombre
 * @property string $apellidos
 * @property string $direccion
 * @property string $telefono
 * @property string $email
 *
 * @property Alumnos[] $alumnos
 * @property Alumnos[] $alumnos0
 */
class Tutores extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tutores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nif', 'nombre', 'apellidos', 'direccion', 'telefono', 'email'], 'required'],
            [['telefono'], 'number'],
            [['nif'], 'string', 'max' => 9],
            [['nombre', 'apellidos', 'direccion', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nif' => 'Nif',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'direccion' => 'Direccion',
            'telefono' => 'Telefono',
            'email' => 'Email',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos()
    {
        return $this->hasMany(Alumnos::className(), ['tutor_id' => 'id'])->inverseOf('tutor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAlumnos0()
    {
        return $this->hasMany(Alumnos::className(), ['tutor2_id' => 'id'])->inverseOf('tutor2');
    }
}
