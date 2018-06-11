<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AlumnosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Alumnos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="alumnos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'options' => [
            'class' => 'escritorio',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'codigo',
            'nombre',
            'primer_apellido',
            'segundo_apellido',
            'fecha_de_nacimiento',
            'dni_primer_tutor',
            'dni_segundo_tutor',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>

    <?= GridView::widget([
        'options' => [
            'class' => 'mobil',
        ],
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'nombre',
            'primer_apellido',
            'dni_primer_tutor',

            ['class' => 'yii\grid\ActionColumn'],
        ],
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
                    <h4>Crear Alumno</h4>
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
