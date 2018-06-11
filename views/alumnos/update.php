<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Alumnos */

$this->title = 'Modificar Alumno: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Alumnos', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="alumnos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
