<?php

use app\models\Uniformes;

use yii\helpers\Html;


?>


<div id='<?= $model->id ?>' class="panel panel-default div-inicial">
    <div class="panel panel-heading contenedor">
        <div class="imagen-uniforme"><img src="<?= $model->uniforme->getRutaImagen() ?>"></div>
        <div class="informacion">
            <div class="informacion-general">
                Descripcion: <?= Html::encode($model->uniforme->descripcion)?></br>
                Talla: <?=Html::encode($model->uniforme->talla)?></br>
                Precio: <?=Html::encode(Yii::$app->formatter->asCurrency($model->uniforme->precio))?></br>
                Cantidad: <?=Html::encode($model->cantidad)?>
            </div>
            <div class="datos-anadir">
                <button class="btn btn-danger boton-borrar" type="button" name="button">Quitar del carro</button>
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
