<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Facturas */

$this->title = 'Create Facturas';
$this->params['breadcrumbs'][] = ['label' => 'Facturas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="facturas-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
