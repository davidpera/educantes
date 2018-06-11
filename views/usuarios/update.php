<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Modificar usuario';
if (Yii::$app->user->id != $model->id && (Yii::$app->user->identity->rol === 'A' || Yii::$app->user->identity->rol === 'C')) {
    $this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = 'Modificar datos usuario';
?>
<div class="usuarios-update">

    <h1 class="titulo"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formUpdate', [
        'model' => $model,
    ]) ?>

</div>
