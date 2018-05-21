<?php

use app\models\Secstocks;

use yii\web\View;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UniformesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Uniformes';
$this->params['breadcrumbs'][] = $this->title;
$urlCantidad = Url::to(['uniformes/cantidad']);
$urlPedido = Url::to(['uniformes/pedido']);
$js = <<<EOT
    var urlCantidad = "$urlCantidad";
    var urlPedido = "$urlPedido";
EOT;
$this->registerJs($js,View::POS_HEAD);
$this->registerJsFile('/js/pedido.js',['depends' => [\yii\web\JqueryAsset::className()]]);
Yii::$app->user->setReturnUrl(Yii::$app->request->url);
?>
<div class="uniformes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php if (Yii::$app->user->identity->rol === 'A' || isset($mio) || Yii::$app->user->identity->rol === 'C'): ?>
    <?php $columnas = [
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
                    if ($model->colegio_id === Yii::$app->user->identity->colegio_id) {
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
                }
            ]
        ],

        ['class' => 'yii\grid\ActionColumn'],
        ] ?>
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
                        <h4>Crear Uniforme</h4>
                    </div>
                    <div class="modal-body">
                        <?= $this->render('_form', [
                            'model' => $model,
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
<?php else: ?>
    <?= Html::button('Hacer pedido multiple',
        [
            'id' => 'pedidoMult',
            'class' => 'btn btn-info',
        ])?>
    <?php $columnas = [
        'codigo',
        'descripcion',
        'talla',
        'precio',
        'iva',
        'cantidad',
        'colegio.nombre',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{pedido}',
            'buttons' => [
                'pedido' => function($url, $model, $key){
                    return Html::button('Hacer pedido',
                                [
                                    'id' => $model->id,
                                    'class' => 'btn btn-success pedido',
                                ]);
                }
            ]
        ],

        ['class' => 'yii\grid\ActionColumn'],
        ] ?>
<?php endif ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $columnas,
]); ?>

<?php if (Yii::$app->user->identity->rol !== 'V'): ?>
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
                    <h4>Crear Uniforme</h4>
                </div>
                <div class="modal-body">
                    <?= $this->render('_form', [
                        'model' => $model,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

</div>
