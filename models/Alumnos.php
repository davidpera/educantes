<?php

namespace app\models;

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
            [['codigo', 'nombre', 'primer_apellido', 'fecha_de_nacimiento', 'dni_primer_tutor', 'dni_segundo_tutor', 'colegio_id'], 'required'],
            [['fecha_de_nacimiento'], 'safe'],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['nombre', 'primer_apellido', 'segundo_apellido', 'unidad'], 'string', 'max' => 255],
            [['codigo'], 'number'],
            [['dni_primer_tutor', 'dni_segundo_tutor'], 'string', 'max' => 9],
            [['codigo', 'dni_primer_tutor', 'dni_segundo_tutor'], 'unique'],
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
            'primer_apellido' => 'Primer Apellido',
            'segundo_apellido' => 'Segundo Apellido',
            'fecha_de_nacimiento' => 'Fecha de nacimiento',
            'dni_primer_tutor' => 'DNI Primer Tutor',
            'dni_segundo_tutor' => 'DNI Segundo Tutor',
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
