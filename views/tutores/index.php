<?php

use app\models\Usuarios;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TutoresSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tutores';
$this->params['breadcrumbs'][] = $this->title;
Yii::$app->user->setReturnUrl(Yii::$app->request->url);
?>
<div class="tutores-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    $columnas = [
        ['class' => 'yii\grid\SerialColumn'],

        'nif',
        'nombre',
        'apellidos',
        'email:email',
        'direccion',
    ];
    $us = Yii::$app->user->identity;
    if ($us->rol === 'A' || $us->rol === 'C') {
        $columnas[] = [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{alta}',
            'buttons' => [
                'alta' => function($url, $model, $key){
                    if (Usuarios::find()->where(['colegio_id' => $model->colegio_id, 'nif' => $model->nif])->count('*') !== 0) {
                        $usc = Usuarios::find()->where(['nif' => $model->nif])->one();
                        return Html::a('Dar de baja', ['usuarios/delete', 'id' => $usc->id], [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => '¿Está seguro de que quiere dar de baja a '.$usc->nom_usuario.'?',
                                    'method' => 'post',
                                ],
                            ]);
                    } else {
                        return Html::a('Dar de alta', ['usuarios/alta', 'colegio_id' => $model->colegio_id, 'idtut' => $model->id],
                        [
                            'class' => 'btn btn-primary',
                        ]);
                    }


                },
            ],
        ];
        $columnas[] = ['class' => 'yii\grid\ActionColumn'];

    }
    // var_dump($columnas[6]);die();
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columnas,
    ]); ?>

    <div class="acciones">
        <?= Html::button('', [
            'class' => 'mas btn glyphicon glyphicon-plus',
            'data-toggle' => 'modal',
            'data-target' => '#formcreate',
            'data-backdrop' => "static"
            ]) ?>
    </div>

    <div class="modal fade" id="formcreate" tabindex="-1" role="dialog" aria-labelledby="label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-cotent">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4>Crear Tutor</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
