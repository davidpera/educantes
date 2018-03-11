<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Detalles */

$this->title = 'Update Detalles: ' . $model->num_detalle;
$this->params['breadcrumbs'][] = ['label' => 'Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->num_detalle, 'url' => ['view', 'num_detalle' => $model->num_detalle, 'factura_id' => $model->factura_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="detalles-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
