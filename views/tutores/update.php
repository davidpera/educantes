<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Tutores */

$this->title = 'Modificar tutor: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Tutores', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tutores-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
