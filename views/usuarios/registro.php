<?php

use yii\helpers\Html;

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Completando registro usuario';
$this->params['breadcrumbs'][] = 'Modificar datos usuario';
?>
<div class="usuarios-registro">

    <h1 class="titulo"><?= Html::encode($this->title) ?></h1>
    <h4 class="titulo">Por favor rellene o corrija todos los parametros mostrados a continuacion</h4>
    <div class="usuarios-form">

        <?php $form = ActiveForm::begin(['options' => ['class' => 'form-update']]); ?>

        <div class="datos">
            <div class="datos-usuario">
                <fieldset>
                    <legend>Datos de usuario</legend>

                    <?= $form->field($model, 'nom_usuario')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'confirmar')->passwordInput(['maxlength' => true]) ?>

                </fieldset>
            </div>

            <div class="datos-personales">
                <fieldset>
                    <legend>Datos personales</legend>

                    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'apellidos')->textInput(['maxlength' => true]) ?>

                    <?php if ($model->rol === 'P'): ?>
                        <span>Si su nif esta incorrecto por favor contacte con su colegio para que lo corrijan</span>
                        <?= $form->field($model, 'nif', ['readonly'=>true])->textInput(['maxlength' => true]) ?>
                    <?php else: ?>
                        <?= $form->field($model, 'nif')->textInput(['maxlength' => true]) ?>
                    <?php endif; ?>

                    <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'tel_movil')->textInput() ?>

                </fieldset>
            </div>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Confirmar', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
