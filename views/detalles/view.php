<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Detalles */

$this->title = $model->num_detalle;
$this->params['breadcrumbs'][] = ['label' => 'Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detalles-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'num_detalle' => $model->num_detalle, 'factura_id' => $model->factura_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'num_detalle' => $model->num_detalle, 'factura_id' => $model->factura_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'num_detalle',
            'factura_id',
            'uniformes_id',
            'cantidad',
        ],
    ]) ?>

</div>
