<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Correos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="correos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'emisario_id')->textInput() ?>

    <?= $form->field($model, 'receptor_id')->textInput() ?>

    <?= $form->field($model, 'mensaje')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
