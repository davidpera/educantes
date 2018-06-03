<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\Carros */

$urlQuitar = Url::to(['uniformes/quitar']);

$this->title = 'Carro de la compra';
$this->params['breadcrumbs'][] = ['label' => 'Carros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = <<<EOT
    $(document).ready(function(){
        eventoBoton();
    });

    function eventoBoton() {
        $('.boton-borrar').on('click', function(){
            var bot = $(this);
            var id = bot.closest('.panel-default').attr('id');
            $.ajax({
                url: "$urlQuitar",
                type: 'POST',
                data: {producto: id},
                    success: function(data){
                        var valor = $('.glyphicon-shopping-cart').text();
                        var regex = /(\d+)/g;
                        var num = parseInt(valor.match(regex)[0]) - 1;
                        $('.glyphicon-shopping-cart').text(' ('+num+')');
                    },
                });
        });
    }
EOT;
$this->registerJs($js);
?>
<div class="carros-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= ListView::widget([
                'options' => [
                    'tag' => 'div',
                    'id' => 'vista-tienda'
                ],
                'dataProvider' => $dataProvider,
                'itemView' => function ($model, $key, $index, $widget) {
                    $itemContent = $this->render('_vistaEnCarro', ['model' => $model]);

                    return $itemContent;
                },
                'itemOptions' => [
                    'tag' => false,
                ],
                'summary' => '',

                'layout' => '{items}{pager}',
            ]) ?>

</div>
