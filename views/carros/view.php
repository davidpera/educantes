<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $model app\models\Carros */

$urlQuitar = Url::to(['uniformes/quitar']);
$urlPedir = Url::to(['carros/pedido']);
if (!isset($pedidos)){
    $this->title = 'Carro de la compra';
} else {
    $this->title = 'Listado pedidos realizados';
}
$this->params['breadcrumbs'][] = $this->title;
$js = <<<EOT
    $(document).ready(function(){
        var alt = document.body.clientHeight * 0.5;
        $('#vista-carro').css({height:alt});
        eventosBotones();
    });

    function eventosBotones() {
        $('.boton-borrar').on('click', function(){
            var regex = /(\d+)/g;
            var bot = $(this);
            var panel = bot.closest('.panel-default');
            var id = panel.attr('id');
            var totaliva = panel.find('#totaliva').children('.dat').text().replace(',', '.');
            console.log(totaliva);
            $.ajax({
                url: "$urlQuitar",
                type: 'POST',
                data: {producto: id},
                    success: function(data){
                        panel.remove();
                        var regex = /(\d+)/g;
                        var totiva = $('#num-total').text().replace(',', '.');
                        var total = round(parseFloat(totiva)) - round(parseFloat(totaliva));
                        var valor = $('.glyphicon-shopping-cart').text();
                        var num = parseInt(valor.match(regex)[0]) - 1;
                        $('#num-total').text(round(total) + ' €');
                        $('.glyphicon-shopping-cart').animate({color: "red"}, 1000).animate({color: "black"}, 1000);
                        $('.glyphicon-shopping-cart').text(' ('+num+')');
                    },
                });
        });

        function round(num, decimales = 2) {
            var signo = (num >= 0 ? 1 : -1);
            num = num * signo;
            if (decimales === 0) //con 0 decimales
                return signo * Math.round(num);
            // round(x * 10 ^ decimales)
            num = num.toString().split('e');
            num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
            // x * 10 ^ (-decimales)
            num = num.toString().split('e');
            return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
        }
        $('.boton-pedido').on('click', function() {
            var todo = new Object();
            var pedidos = [];
            for (ped of $('.informacion-general')) {
                // console.log(ped);
                var pedido = [];
                for (sp of ped.children) {
                    if (sp.className === "infor") {
                        // var col = sp.id;
                        // console.log(sp.id);
                        // console.log(sp.children[0].innerText);
                        if (sp.id == "id") {
                            pedido.push(sp.innerText);
                        } else {
                            pedido.push(sp.children[0].innerText);
                        }
                        // pedido[col] = sp.children[0].innerText;
                    }
                }
                pedidos.push(pedido);
            }
            todo.pedidos = pedidos;
            var json = JSON.stringify(todo);
            $.ajax({
                url: "$urlPedir",
                type: 'POST',
                data: {pedido: json},
                success: function(data){
                    // document.write(data);
                    console.log(data);
                    // var valor = $('.glyphicon-shopping-cart').text();
                    // var regex = /(\d+)/g;
                    // var num = parseInt(valor.match(regex)[0]) - 1;
                    // $('.glyphicon-shopping-cart').text(' ('+num+')');
                },
            });
        });
    }
EOT;
$this->registerJs($js);
$this->registerJsFile('/js/jquery_color.js',['depends' => [\yii\web\JqueryAsset::className()]]);

$totalIva = 0;
$productos = $dataProvider->query->all();
foreach ($productos as $prod) {
    $total = $prod->uniforme->precio * $prod->cantidad;
    $totalIva += $total + ($total * ($prod->uniforme->iva/100));
}
$total = $totalIva;
?>
<div class="carros-view">

    <h1 class="titulo"><?= Html::encode($this->title) ?></h1>

    <?= ListView::widget([
                'options' => [
                    'tag' => 'div',
                    'id' => 'vista-carro'
                ],
                'dataProvider' => $dataProvider,
                'itemView' => function ($model, $key, $index, $widget) {
                    if ($model->realizado) {
                        $itemContent = $this->render('_vistaEnCarro', ['model' => $model, 'pedidos' => true]);
                    } else {
                        $itemContent = $this->render('_vistaEnCarro', ['model' => $model]);
                    }

                    return $itemContent;
                },
                'itemOptions' => [
                    'tag' => false,
                ],
                'summary' => '',

                'layout' => '{items}{pager}',
            ]) ?>

    <div class="titulo">
        <?php if (!isset($pedidos)): ?>
            <div class="Total-iva">
                <h3>Total de los productos con iva: <span id="num-total"><?= Yii::$app->formatter->asCurrency($total) ?></span> </h3>
            </div>
            <div class="botones">
                <button class="btn btn-success boton-pedido" type="button" name="button">Realizar pedido</button>
            </div>
        <?php else: ?>
            <div class="Total-iva">
                <h3>Total gastado con iva: <span id="num-total"><?= Yii::$app->formatter->asCurrency($total) ?></span> </h3>
            </div>
        <?php endif; ?>
    </div>

</div>
