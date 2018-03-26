<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SecstocksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Secstocks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="secstocks-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Secstocks', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cd',
            'pe',
            'ss',
            'mp',
            //'uniforme_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
