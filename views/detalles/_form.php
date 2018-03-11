<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Detalles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalles-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'factura_id')->textInput() ?>

    <?= $form->field($model, 'uniformes_id')->textInput() ?>

    <?= $form->field($model, 'cantidad')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
