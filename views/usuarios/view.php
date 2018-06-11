<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = $model->nom_usuario;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Modificar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Borrar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php
    $attributes = [
        'nom_usuario',
        'nombre',
        'apellidos',
        'nif',
        'direccion',
        'email:email',
        'tel_movil',
        [
            'label'  => 'Rol',
            'value'  => function($model){
                switch ($model->rol) {
                    case 'A':
                        return "Administrador general";
                        break;
                    case 'C':
                        return "Administrador de colegio";
                        break;
                    case 'V':
                        return "Vendedor";
                        break;
                    case 'P':
                        return "Padre";
                        break;
                }
            }
        ],
    ];
  ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => $attributes,
    ]) ?>

</div>
