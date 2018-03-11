<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Uniformes */

$this->title = 'Create Uniformes';
$this->params['breadcrumbs'][] = ['label' => 'Uniformes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uniformes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
