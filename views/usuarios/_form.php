<?php

use app\models\Colegios;

use yii\web\View;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-form">

    <?php $form = ActiveForm::begin(['id' => 'formulario']); ?>

    <?= $form->field($model, 'nom_usuario', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'confirmar')->passwordInput(['maxlength' => true]) ?>

    <?php if(Yii::$app->user->isGuest): ?>

        <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'nif', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email', ['enableAjaxValidation' => true])->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tel_movil', ['enableAjaxValidation' => true])->textInput() ?>

        <?php $var = \yii\helpers\ArrayHelper::map(Colegios::find()->all(), 'id', 'nombre'); ?>
        <?= $form->field($model, 'colegio_id')->dropDownList($var) ?>

    <?php endif; ?>

    <div id="botones" class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
