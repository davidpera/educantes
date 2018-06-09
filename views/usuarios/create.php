<?php

use yii\web\View;

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Crear Usuario';
if (Yii::$app->user->identity->rol === 'C') {
    $this->title .= ' Vendedor';
} else {
    $this->title .= ' Administrador de colegio';
}
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <span>Se le mandara un correo al email que ponga en este formulario para que
    esa persona pueda terminar de registrarse</span>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
