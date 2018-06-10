<?php

use app\models\Usuarios;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->user->setReturnUrl(Yii::$app->request->url);
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $columnas = [

        'nom_usuario',
        'nombre',
        'apellidos',
        // 'nif',
        // 'direccion',
        'email:email',
        'tel_movil',
        // 'rol',
    ];
    if (Yii::$app->user->identity->rol === 'A') {
        $columnas[] = 'colegio.nombre';
    }
    $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
    if ($us->rol === 'A' || $us->rol === 'C') {
        $columnas[] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update} {baja}',
            'buttons' => [
                'baja' => function($url, $model, $key){
                        $usc = Usuarios::find()->where(['<>', 'id', Yii::$app->user->id])->all();
                        for ($i=0; $i < count($usc); $i++) {
                            if ($model->id === $usc[$i]->id) {
                                return Html::a('', ['usuarios/delete', 'id' => $usc[$i]->id], [
                                        'class' => 'glyphicon glyphicon-trash',
                                        'data' => [
                                            'confirm' => '¿Está seguro de que quiere dar de baja a '.$usc[$i]->nom_usuario.'?',
                                            'method' => 'post',
                                        ],
                                    ]);
                            }

                        }
                },
            ]
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
            'nom_usuario',
            'email:email',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {baja}',
                'buttons' => [
                    'baja' => function($url, $model, $key){
                            $usc = Usuarios::find()->where(['<>', 'id', Yii::$app->user->id])->all();
                            for ($i=0; $i < count($usc); $i++) {
                                if ($model->id === $usc[$i]->id) {
                                    return Html::a('', ['usuarios/delete', 'id' => $usc[$i]->id], [
                                            'class' => 'glyphicon glyphicon-trash',
                                            'data' => [
                                                'confirm' => '¿Está seguro de que quiere dar de baja a '.$usc[$i]->nom_usuario.'?',
                                                'method' => 'post',
                                            ],
                                        ]);
                                }

                            }
                    },
                ]
            ],
        ],
    ]); ?>
</div>
