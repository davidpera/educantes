<?php

use app\models\Usuarios;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $columnas = [
        ['class' => 'yii\grid\SerialColumn'],

        'nom_usuario',
        'nombre',
        'apellidos',
        'nif',
        'direccion',
        'email:email',
        'tel_movil',
        'rol',
        'colegio.nombre',
    ];
    $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
    if ($us->rol === 'A' || $us->rol === 'C') {
        $columnas[] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{alta}',
            'buttons' => [
                'alta' => function($url, $model, $key){
                    $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
                    if ($us->rol === 'A') {
                        $usc = Usuarios::find()->where(['<>', 'id', Yii::$app->user->id])->all();
                        for ($i=0; $i < count($usc); $i++) {
                            if ($model->id === $usc[$i]->id) {
                                return Html::a('Dar de baja usuario', ['usuarios/delete', 'id' => $usc[$i]->id], [
                                        'class' => 'btn btn-danger',
                                        'data' => [
                                            'confirm' => '¿Está seguro de que quiere dar de baja a '.$usc[$i]->nom_usuario.'?',
                                            'method' => 'post',
                                        ],
                                    ]);
                            }

                        }
                    } elseif ($us->rol === 'C' && $us->colegio_id === $model->colegio_id) {
                        $usv = Usuarios::find()->andWhere(['colegio_id' => $us->colegio_id])
                        ->andWhere(['or',
                           ['rol' => 'V'],
                           ['rol' => 'P'],
                        ])->one();
                        if ($usv->rol === 'V') {
                            return Html::a('Dar de baja vendedor', ['usuarios/delete', 'id' => $model->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => '¿Está seguro de que quiere dar de baja a '.$model->nom_usuario.'?',
                                        'method' => 'post',
                                    ],
                                ]);
                        }else{
                            return Html::a('Dar de baja padre', ['usuarios/delete', 'id' => $usv->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => '¿Está seguro de que quiere dar de baja a '.$usv->nom_usuario.'?',
                                        'method' => 'post',
                                    ],
                                ]);
                        }

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
