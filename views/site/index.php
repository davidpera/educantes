<?php

// use Yii;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
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
        <div class="contenedor-imagen">
            <img src="uploads/foto3.jpg" alt="" class="lema">
        </div>
        <div class="contenedor-imagen">
            <img src="uploads/foto1.jpg" alt="" class="lema">
        </div>
        <div class="central">
            <h3>Registrese o conectese para realizar acciones</h3>
        </div>
        <div class="contenedor-imagen">
            <img src="uploads/lema1.jpg" alt="" class="lema">
        </div>
        <div class="contenedor-imagen">
            <img src="uploads/lema3.jpg" alt="" class="lema">
        </div>
    </div>
<?php endif ?>
</div>
