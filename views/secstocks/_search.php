<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SecstocksSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="secstocks-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cd') ?>

    <?= $form->field($model, 'pe') ?>

    <?= $form->field($model, 'ss') ?>

    <?= $form->field($model, 'mp') ?>

    <?php // echo $form->field($model, 'uniforme_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
