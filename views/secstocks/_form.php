<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Secstocks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="secstocks-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cd')->textInput() ?>

    <?= $form->field($model, 'pe')->textInput() ?>

    <?= $form->field($model, 'ss')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
