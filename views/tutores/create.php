<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tutores */

$this->title = 'Crear tutor';
$this->params['breadcrumbs'][] = ['label' => 'Tutores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tutores-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
