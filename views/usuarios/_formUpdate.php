<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-form">

    <?php $form = ActiveForm::begin(['options' => ['class' => 'form-update']]); ?>

    <div class="datos">
        <div class="datos-usuario">
            <fieldset>
                <legend>Datos de usuario</legend>

                <?= $form->field($model, 'nom_usuario')->textInput(['maxlength' => true]) ?>

                <?php if (Yii::$app->user->id == $model->id || (Yii::$app->user->identity->rol !== 'A' && Yii::$app->user->identity->rol !== 'C')): ?>
                    <?= $form->field($model, 'viejaPassword')->passwordInput(['maxlength' => true]) ?>
                <?php endif; ?>

                <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'confirmar')->passwordInput(['maxlength' => true]) ?>

            </fieldset>
        </div>

        <div class="datos-personales">
            <fieldset>
                <legend>Datos personales</legend>

                <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'nif')->textInput(['maxlength' => 9]) ?>

                <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'tel_movil')->textInput(['maxlength' => 9]) ?>

            </fieldset>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Confirmar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
