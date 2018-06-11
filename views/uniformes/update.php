<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Uniformes */

$this->title = 'Modificar uniforme: ' . $model->codigo;
$this->params['breadcrumbs'][] = ['label' => 'Uniformes', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="uniformes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
