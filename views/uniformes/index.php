<?php

use app\models\Secstocks;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UniformesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Uniformes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="uniformes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'codigo',
            'descripcion',
            'talla',
            'precio',
            'iva',
            'ubicacion',
            'cantidad',
            'colegio.nombre',
            'secstock.mp',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{segStock}',
                'buttons' => [
                    'segStock' => function($url, $model, $key){
                        if (Secstocks::find()->where(['uniforme_id' => $model->id])->count('*') === 0) {
                            return Html::a('Añadir stock de seguridad', ['secstocks/create', 'uniforme_id' => $model->id],
                                [
                                    'class' => 'btn btn-success',
                                ]);
                        } else {
                            return Html::a('Modificar stock de seguridad', ['secstocks/update', 'uniforme_id' => $model->id],
                                [
                                    'class' => 'btn btn-primary',
                                ]);
                        }
                    }
                ]
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
