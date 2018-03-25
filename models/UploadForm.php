<?php

namespace app\models;

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
            $this->file_alum->saveAs('uploads/' . $this->file_alum->baseName . '.' . $this->file_alum->extension);
            return true;
        }
        return false;
    }
}
