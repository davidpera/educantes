<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
?>
<div>
    <?php
    $form = ActiveForm::begin([
            'options' => [ 'enctype' => 'multipart/form-data']
    ]);
    ?>
    <?= $form->field($model, 'file_alum')->fileInput(); ?>

    <div class="form-group">
        <?= Html::submitButton('Confirmar', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
