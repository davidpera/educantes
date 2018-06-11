<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Secstocks */

$this->title = 'Modificar stock de seguridad uniforme: ' . $model->uniforme->codigo;
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="secstocks-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
