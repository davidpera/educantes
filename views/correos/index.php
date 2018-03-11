<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CorreosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Correos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="correos-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Correos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'emisario_id',
            'receptor_id',
            'mensaje',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
