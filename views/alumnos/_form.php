<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Alumnos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="alumnos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'codigo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unidad')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'primer_apellido')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'segundo_apellido')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_de_nacimiento')->textInput() ?>

    <?= $form->field($model, 'dni_primer_tutor')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dni_segundo_tutor')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
