<?php

// use Yii;
/* @var $this yii\web\View */

$js = <<<EOT
$(document).ready(function(){
    $('#my-slide').DrSlider({
        'navigationType': 'circle',
        'positionNavigation': 'in-center-bottom',
        'transition': 'fade'
    });
});
EOT;
$this->title = 'Educantes';
$this->registerJs($js);
$this->registerJsFile('/js/devrama_slider.js',['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<div class="site-index">
<?php if (!Yii::$app->user->isGuest) : ?>
    <div class="jumbotron">
        <img src="/uploads/Educantes2.png" alt="Educantes" class="imagen">
            <p>
                Bienvenido <?= Yii::$app->user->identity->nom_usuario ?>
            </p>
    </div>
<?php else: ?>
    <div class="padre">
        <!-- <div class="contenedor-imagen">
            <img src="uploads/foto3.jpg" alt="" class="lema">
        </div>
        <div class="contenedor-imagen">
            <img src="uploads/foto1.jpg" alt="" class="lema">
        </div> -->
        <div class="central">
            <h3>Conectese para realizar acciones</h3>
        </div>
        <!-- <div class="contenedor-imagen">
            <img src="uploads/lema1.jpg" alt="" class="lema">
        </div>
        <div class="contenedor-imagen">
            <img src="uploads/lema3.jpg" alt="" class="lema">
        </div> -->
        <div class="inferior">
            <div id="my-slide">
                <img data-lazy-src="uploads/foto3.jpg" />
                <img data-lazy-src="uploads/foto1.jpg" />
                <img data-lazy-src="uploads/lema1.jpg" />
                <img data-lazy-src="uploads/lema3.jpg" />
            </div>
        </div>
    </div>
<?php endif ?>
</div>
