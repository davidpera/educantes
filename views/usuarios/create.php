<?php

use yii\web\View;

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Create Usuarios';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/comprobacionAlumno.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<div class="usuarios-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
