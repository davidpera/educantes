<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = 'Modificar usuario';
$this->params['breadcrumbs'][] = 'Modificar datos usuario';
?>
<div class="usuarios-update">

    <h1 class="titulo"><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formUpdate', [
        'model' => $model,
    ]) ?>

</div>
