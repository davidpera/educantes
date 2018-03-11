<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "correos".
 *
 * @property int $id
 * @property int $emisario_id
 * @property int $receptor_id
 * @property string $mensaje
 *
 * @property Colegios $emisario
 * @property Usuarios $receptor
 */
class Correos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'correos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emisario_id', 'receptor_id', 'mensaje'], 'required'],
            [['emisario_id', 'receptor_id'], 'default', 'value' => null],
            [['emisario_id', 'receptor_id'], 'integer'],
            [['mensaje'], 'string', 'max' => 255],
            [['emisario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Colegios::className(), 'targetAttribute' => ['emisario_id' => 'id']],
            [['receptor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['receptor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emisario_id' => 'Emisario ID',
            'receptor_id' => 'Receptor ID',
            'mensaje' => 'Mensaje',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmisario()
    {
        return $this->hasOne(Colegios::className(), ['id' => 'emisario_id'])->inverseOf('correos');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceptor()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'receptor_id'])->inverseOf('correos');
    }
}
