<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file_alum;

    public function rules()
    {
        return [
            [['file_alum'], 'file', 'skipOnEmpty' => false],
        ];
    }
    public function attributeLabels()
    {
        return [
            'file_alum' => 'Archivo',
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $nombre = Yii::getAlias('@uploads/') . $this->file_alum->baseName . '.' . $this->file_alum->extension;
            $this->file_alum->saveAs($nombre);
            return true;
        }
        return false;
    }
}
