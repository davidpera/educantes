<?php

use app\models\Secstocks;
use app\models\Uniformes;

use yii\web\View;
use yii\web\Session;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;

use yii\widgets\ListView;

use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UniformesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Uniformes';
$this->params['breadcrumbs'][] = $this->title;
$urlCantidad = Url::to(['uniformes/cantidad']);
$urlPedido = Url::to(['uniformes/pedido']);
$urlAnadir = Url::to(['uniformes/anadir']);

if (Yii::$app->user->identity->rol === 'P') {
    $js = <<<EOT
        $(document).ready(function(){
            var alt = document.body.clientHeight * 0.6;
            $('#vista-tienda').css({height:alt});
            $('.numeric').children('input').val(0);
            anadirFiltrado();
            eventoBoton();
            eventoNumeric();
        });

        function anadirFiltrado(){
            $('.filtrado').append('<input type="text"></input>');
            eventoFiltrado();
        }

        function eventoFiltrado(){
            $('.filtrado').children('input').on('keyup', function(){
                $('.producto').closest('.div-inicial').hide();
                $('.producto[id*="'+$(this).val().trim()+'"]').closest('.div-inicial').show();
                if ($(this).val().trim() === '') {
                    $('.producto').closest('.div-inicial').show();
                }
            });
        }

        function eventoNumeric(){
            $('.numeric').children('input').on('change', function(){
                $(this).closest('.numeric').children('p').remove();
                $(this).closest('.numeric').children('input').removeClass('error-input');
            });
        }

        function eventoBoton() {
            $('.boton-anadir').children('button').on('click', function(){
                var bot = $(this);
                var id = bot.closest('.panel-default').attr('id');
                var numer = bot.closest('.informacion').children('.datos-anadir').children('.numeric');
                var cant = numer.children('input').val();
                numer.children('input').val(0);
                // console.log(cant);
                if (cant !== "0") {
                    $('.numeric').remove('p');
                    numer.children('input').removeClass('error-input');
                    $.ajax({
                        url: "$urlAnadir",
                        type: 'POST',
                        data: {uniforme: id, cantidad: cant},
                        success: function(data){
                            console.log(data);
                            var valor = $('.glyphicon-shopping-cart').text();
                            var regex = /(\d+)/g;
                            var num = parseInt(valor.match(regex)[0]) + 1;
                            $('.glyphicon-shopping-cart').animate({color: "red"}, 1000).animate({color: "black"}, 1000);
                            $('.glyphicon-shopping-cart').text(' ('+num+')');
                            numer.children('input').attr('max', numer.children('input').attr('max') - cant);
                        },
                    });
                } else {
                    numer.children('p').remove();
                    numer.children('input').removeClass('error-input');
                    numer.append('<p class="error">La cantidad de uniformes no puede ser 0</p>');
                    numer.children('input').addClass('error-input');
                }
            });
        }
EOT;
$this->registerJsFile('/js/jquery_color.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs($js);
} else {
    $js = <<<EOT
        var urlCantidad = "$urlCantidad";
        var urlPedido = "$urlPedido";
EOT;
$this->registerJs($js,View::POS_HEAD);
$this->registerJsFile('/js/pedido.js',['depends' => [\yii\web\JqueryAsset::className()]]);
}

Yii::$app->user->setReturnUrl(Yii::$app->request->url);
?>
<div class="uniformes-index">

    <h1 class="titulo"><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?php if (Yii::$app->user->identity->rol === 'P'): ?>
    <div class="filtrado">
        <span>Filtrado: </span>
    </div>
    <?= ListView::widget([
                'options' => [
                    'tag' => 'div',
                    'id' => 'vista-tienda'
                ],
                'dataProvider' => $mioProvider,
                'itemView' => function ($model, $key, $index, $widget) {
                    $itemContent = $this->render('_vistaTienda', ['model' => $model]);

                    return $itemContent;
                },
                'itemOptions' => [
                    'tag' => false,
                ],
                'summary' => '',

                'layout' => '{items}{pager}',
            ]) ?>
<?php else: ?>
    <?php if (Yii::$app->user->identity->rol === 'C'): ?>
        <?= GridView::widget([
            'dataProvider' => $mioProvider,
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

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{update} {delete}'
                ],
            ],
        ])?>
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
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'Mi colegio',
                    'content' => GridView::widget([
                        'dataProvider' => $mioProvider,
                        'filterModel' => $searchModel,
                        'options' => [
                            'class' => 'uniformes-escritorio'
                        ],
                        'columns' => [
                            'codigo',
                            'descripcion',
                            'talla',
                            'precio',
                            'iva',
                            'ubicacion',
                            'cantidad',
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

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}'
                            ],
                        ],
                    ]) . GridView::widget([
                        'options' => [
                            'class' => 'uniformes-mobil'
                        ],
                        'dataProvider' => $mioProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            'codigo',
                            'precio',
                            'cantidad',

                            ['class' => 'yii\grid\ActionColumn'],
                            ],
                    ]) . "<div class='acciones'>".
                        Html::button('', [
                            'class' => 'mas btn glyphicon glyphicon-plus',
                            'data-toggle' => 'modal',
                            'data-target' => '#formcreate',
                            'data-backdrop' => 'static'
                            ]) .
                    "</div>".

                    "<div class='modal fade' id='formcreate' tabindex='-1' role='dialog' aria-labelledby='label' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-cotent'>
                                <div class='modal-header'>
                                    <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                                    <h4>Crear Uniforme</h4>
                                </div>
                                <div class='modal-body'>".
                                    $this->render('_form', [
                                        'model' => $model,
                                    ]) .
                                "</div>
                            </div>
                        </div>
                    </div>" ,
                    'headerOptions' => [
                        'id' => 'mio',
                        'class' => 'pestañas'
                    ],
                ],
                [
                    'label' => 'Otros colegios',
                    'content' => "<div class='ped-mul'>".
                    Html::button('Hacer pedido multiple',
                        [
                            'id' => 'pedidoMult',
                            'class' => 'btn btn-info',
                        ]) . "</div>" . GridView::widget([
                            'options' => [
                                'class' => 'uniformes-escritorio'
                            ],
                        'dataProvider' => $otroProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
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

                            ],
                    ]) . GridView::widget([
                        'options' => [
                            'class' => 'uniformes-mobil'
                        ],
                        'dataProvider' => $otroProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            'codigo',
                            'precio',
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

                            ],
                    ]) . GridView::widget([
                            'options' => [
                                'class' => 'uniformes-mobilPequeno'
                            ],
                            'dataProvider' => $otroProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                'codigo',
                                'cantidad',
                                'colegio.nombre',
                            ],
                        ]),
                    'headerOptions' => [
                        'id' => 'otros',
                        'class' => 'pestañas'
                    ],
                ]
            ],
        ]);?>
    <?php endif ?>
<?php endif; ?>


</div>
