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

    <?php
    $columnas = [

        'cif',
        'nombre',
        'email:email',
        'cod_postal',
        'direccion',
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
                        if (Usuarios::find()->where(['colegio_id' => $model->id])->count('*') !== 0) {
                            $usc = Usuarios::find()->where(['colegio_id' => $model->id , 'rol' => 'C'])->one();
                            return Html::a('Dar de baja admin', ['usuarios/delete', 'id' => $usc->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => '¿Está seguro de que quiere dar de baja a '.$usc->nom_usuario.'?',
                                        'method' => 'post',
                                    ],
                                ]);
                        } else {
                            return Html::a('Dar de alta admin', ['usuarios/alta', 'colegio_id' => $model->id],
                                [
                                    'class' => 'btn btn-primary',
                                ]);
                        }
                    } elseif ($us->rol === 'C' && $us->colegio_id === $model->id) {
                        if (Usuarios::find()->where(['colegio_id' => $model->id, 'rol' => 'V'])->count('*') !== 0) {
                            $usv = Usuarios::find()->where(['colegio_id' => $model->id , 'rol' => 'V'])->one();
                            return Html::a('Dar de baja Vendedor', ['usuarios/delete', 'id' => $usv->id], [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => '¿Está seguro de que quiere dar de baja a '.$usv->nom_usuario.'?',
                                        'method' => 'post',
                                    ],
                                ]);
                        } else {
                            return Html::a('Dar de alta Vendedor', ['usuarios/alta', 'colegio_id' => $model->id],
                                [
                                    'class' => 'btn btn-primary',
                                ]);
                        }

                    }
                },
            ],
        ];

    }
    // var_dump($columnas[6]);die();
    ?>
    <?= GridView::widget([
        'options' => [
            'class' => 'escritorio',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnas,
    ]); ?>

    <?= GridView::widget([
        'options' => [
            'class' => 'mobil',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'cif',
            'nombre',
                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{alta}',
                    'buttons' => [
                        'alta' => function($url, $model, $key){
                            $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
                            if ($us->rol === 'A') {
                                if (Usuarios::find()->where(['colegio_id' => $model->id])->count('*') !== 0) {
                                    $usc = Usuarios::find()->where(['colegio_id' => $model->id , 'rol' => 'C'])->one();
                                    return Html::a('Baja admin', ['usuarios/delete', 'id' => $usc->id], [
                                            'class' => 'btn btn-danger',
                                            'data' => [
                                                'confirm' => '¿Está seguro de que quiere dar de baja a '.$usc->nom_usuario.'?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                } else {
                                    return Html::a('Alta admin', ['usuarios/alta', 'colegio_id' => $model->id],
                                        [
                                            'class' => 'btn btn-primary',
                                        ]);
                                }
                            } elseif ($us->rol === 'C' && $us->colegio_id === $model->id) {
                                if (Usuarios::find()->where(['colegio_id' => $model->id, 'rol' => 'V'])->count('*') !== 0) {
                                    $usv = Usuarios::find()->where(['colegio_id' => $model->id , 'rol' => 'V'])->one();
                                    return Html::a('Baja Vendedor', ['usuarios/delete', 'id' => $usv->id], [
                                            'class' => 'btn btn-danger',
                                            'data' => [
                                                'confirm' => '¿Está seguro de que quiere dar de baja a '.$usv->nom_usuario.'?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                } else {
                                    return Html::a('Alta Vendedor', ['usuarios/alta', 'colegio_id' => $model->id],
                                        [
                                            'class' => 'btn btn-primary',
                                        ]);
                                }

                            }
                        },
                    ],
                ]

        ],
    ]); ?>
</div>
