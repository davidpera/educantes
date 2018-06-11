<?php

// use Yii;
/* @var $this yii\web\View */
use app\models\Usuarios;


$js = <<<EOT
$(document).ready(function(){
    var nav = "";
    var agente = window.navigator.userAgent;
    var navegadores = ["Chrome", "Firefox", "Safari", "Opera", "Trident", "MSIE", "Edge"];
    for(var i in navegadores){
        if(agente.indexOf( navegadores[i]) != -1 ){
            nav = navegadores[i];
        }
    }
    console.log(agente);
    if (nav == "Safari") {
        $('.video').css({"margin-top":"-35px"});
    }
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
$email = Usuarios::findOne(['rol' => 'A'])->email;
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
    <div class="padre" itemscope itemtype="http://schema.org/Organization">
        <div class="hijo">
            <h2>Conectese para realizar acciones</h2>
        </div>
        <div class="superior">
            <video class="video" controls autoplay muted>
              <source src="/uploads/video.mp4" type="video/mp4">
              Your browser does not support the video tag.
            </video>
            <div id="my-slide">
                <img alt="Foto sobre educacion" src="uploads/foto3.jpg" data-lazy-src="uploads/foto3.jpg" />
                <img alt="Foto sobre educacion" src="uploads/foto1.jpg" data-lazy-src="uploads/foto1.jpg" />
                <img alt="Foto sobre educacion" src="uploads/lema1.jpg" data-lazy-src="uploads/lema1.jpg" />
                <img alt="Foto sobre educacion" src="uploads/lema3.jpg" data-lazy-src="uploads/lema3.jpg" />
            </div>
        </div>
        <div class="inferior titulo">
            <h3 itemprop="legalName" ><?= $this->title ?></h3>
            <h4>Email Administrador: <span itemprop="email"><?= $email ?></span></h4>
        </div>
    </div>
<?php endif ?>
</div>
