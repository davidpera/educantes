<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Secstocks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="secstocks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cd')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pe')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'ss')->textInput(['maxlength' => 10]) ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
