<?php

use Yii;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <img src="/uploads/Educantes2.png" alt="Educantes" class="imagen">
        <?php if (Yii::$app->user->isGuest) : ?>
            <p>
                Registrese o conectese para poder realizar acciones
            </p>
        <?php else: ?>
            <p>
                Bienvenido <?= Yii::$app->user->identity->nom_usuario ?>
            </p>
        <?php endif ?>
    </div>
</div>
