<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Logeo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login col-md-10 col-md-offset-4 formul">
    <h1 class="col-md-offset-1"><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <!-- <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input} {label}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ]) ?> -->

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Confirmar', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::a('¿Ha olvidado su contraseña? Haz click aqui',['/usuarios/olvidado']); ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
