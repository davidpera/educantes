<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Sms */

$this->title = 'Create Sms';
$this->params['breadcrumbs'][] = ['label' => 'Sms', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
