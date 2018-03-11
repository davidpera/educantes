<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "alumnos".
 *
 * @property int $id
 * @property string $codigo
 * @property string $nombre
 * @property string $apellidos
 * @property string $fech_nac
 * @property string $nom_padre
 * @property string $nom_madre
 * @property int $colegio_id
 *
 * @property Colegios $colegio
 */
class Alumnos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'alumnos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['codigo', 'nombre', 'apellidos', 'fech_nac', 'nom_padre', 'nom_madre', 'colegio_id'], 'required'],
            [['fech_nac'], 'safe'],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['codigo', 'nombre', 'apellidos', 'nom_padre', 'nom_madre'], 'string', 'max' => 255],
            [['codigo'], 'unique'],
            [['colegio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Colegios::className(), 'targetAttribute' => ['colegio_id' => 'id']],
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
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'fech_nac' => 'Fech Nac',
            'nom_padre' => 'Nom Padre',
            'nom_madre' => 'Nom Madre',
            'colegio_id' => 'Colegio ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColegio()
    {
        return $this->hasOne(Colegios::className(), ['id' => 'colegio_id'])->inverseOf('alumnos');
    }
}
