<?php

namespace app\models;

/**
 * This is the model class for table "libros".
 *
 * @property int $id
 * @property string $isbn
 * @property string $titulo
 * @property string $curso
 * @property string $precio
 * @property int $colegio_id
 *
 * @property Colegios $colegio
 */
class Libros extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'libros';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['isbn', 'titulo', 'curso', 'precio', 'colegio_id'], 'required'],
            [['isbn', 'precio'], 'number'],
            [['isbn'], 'match', 'pattern' => '/^\d{13}$/'],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['titulo', 'curso'], 'string', 'max' => 255],
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
            'isbn' => 'ISBN',
            'titulo' => 'Titulo',
            'curso' => 'Curso',
            'precio' => 'Precio',
            'colegio_id' => 'Colegio ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColegio()
    {
        return $this->hasOne(Colegios::className(), ['id' => 'colegio_id'])->inverseOf('libros');
    }
}
