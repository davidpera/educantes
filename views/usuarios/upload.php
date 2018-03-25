<?php

use app\models\Alumnos;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
?>
<div>
    <h3>Introduzca un documento .xlsx con el listado de datos de su
    colegio teniendo en cuenta que la primera linea del archivo sea
    la que contiene el titulo de la columna</h3>
    <?php
    $form = ActiveForm::begin([
            'options' => [ 'enctype' => 'multipart/form-data']
    ]);
    ?>
    <?= $form->field($model, 'file_alum')->fileInput(['options' => ['accept' => 'application/vnd.ms-excel']]); ?>

    <div class="form-group">
        <?= Html::submitButton('Confirmar', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
