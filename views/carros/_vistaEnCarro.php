<?php

use app\models\Uniformes;

use yii\helpers\Html;

// var_dump($pedidos);
// die();
?>


<div id='<?= $model->id ?>' class="panel panel-default div-inicial">
    <div class="panel panel-heading contenedor">
        <div class="imagen-uniforme"><img src="<?= $model->uniforme->getRutaImagen() ?>"></div>
        <div class="informacion">
            <div class="informacion-general">
                <span id="descripcion" class="infor">Descripcion: <span class="dat"><?= Html::encode($model->uniforme->descripcion)?></span></span></br>
                <span id="talla" class="infor">Talla: <span class="dat"><?=Html::encode($model->uniforme->talla)?></span></span></br>
                <span id="precio" class="infor">Precio/U: <span class="dat"><?=Html::encode(Yii::$app->formatter->asCurrency($model->uniforme->precio))?></span></span></br>
                <span id="cantidad" class="infor">Cantidad: <span class="dat"><?=Html::encode($model->cantidad)?></span></span></br>
                <span id="total" class="infor">Total: <span class="dat"><?= Html::encode(Yii::$app->formatter->asCurrency(($model->uniforme->precio * $model->cantidad))) ?></span></span>
                <span id="id" class="infor" hidden><?=$model->id?></span>
            </div>
            <div class="datos-anadir">
                <?php if (!isset($pedidos)): ?>
                    <button class="btn btn-danger boton-borrar" type="button" name="button">Quitar del carro</button>
                <?php else: ?>
                    Pedido el: <?= Yii::$app->formatter->asDatetime($model->fecha_pedido) ?>
                    <?php if ($model->aceptado): ?>
                        <h4 class="verde">Aceptado</h4>
                    <?php else: ?>
                        <h4 class="azul">En progreso de aceptacion</h4>
                    <?php endif; ?>
                <?php endif; ?>
                <!-- <div class="numeric">
                    Cantidad: <input class="cantidad form-control" type="number" value='0' min="0" max="<?= $model->cantidad ?>"></br>
                </div> -->
                <!-- <div class="boton-anadir">
                    <button type="button" name="button">Añadir a carrito</button>
                </div> -->
            </div>
        </div>
    </div>
</div>
