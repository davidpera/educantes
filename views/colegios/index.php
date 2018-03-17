<?php

use app\models\Usuarios;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ColegiosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Colegios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="colegios-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Colegios', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php
    $columnas = [
        ['class' => 'yii\grid\SerialColumn'],

        'cif',
        'nombre',
        'email:email',
        'cod_postal',
        'direccion',
    ];
    if (Usuarios::find()->where(['id' => Yii::$app->user->id])->one()->rol === 'A') {
        $columnas[] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{alta}',
            'buttons' => [
                'alta' => function($url, $model, $key){
                    if (Usuarios::find()->where(['colegio_id' => $model->id])->count('*') !== 0) {
                        $us = Usuarios::find()->where(['colegio_id' => $model->id , 'rol' => 'C'])->one();
                        return Html::a('Dar de baja admin', ['usuarios/delete', 'id' => $us->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => '¿Está seguro de que quiere dar de baja a '.$us->nom_usuario.'?',
                                    'method' => 'post',
                                ],
                            ]);
                    } else {
                        return Html::a('Dar de alta admin', ['usuarios/alta', 'colegio_id' => $model->id],
                            [
                                'class' => 'btn btn-primary',
                            ]);
                    }

                },
            ],
        ];;
        $columnas[] = ['class' => 'yii\grid\ActionColumn'];

    }
    // var_dump($columnas[6]);die();
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnas,
    ]); ?>
</div>
