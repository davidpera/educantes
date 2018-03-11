<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Colegios */

$this->title = 'Create Colegios';
$this->params['breadcrumbs'][] = ['label' => 'Colegios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="colegios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
