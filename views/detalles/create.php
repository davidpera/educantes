<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Detalles */

$this->title = 'Create Detalles';
$this->params['breadcrumbs'][] = ['label' => 'Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="detalles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
