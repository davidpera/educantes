<?php

use app\models\Uniformes;

use yii\helpers\Html;


?>


<div id='<?= $model->id ?>' class="panel panel-default div-inicial">
    <div class="panel panel-heading contenedor">
        <div class="imagen-uniforme"><img src="<?= $model->getRutaImagen() ?>"></div>
        <div class="informacion">
            <div class="informacion-general">
                Descripcion: <?= Html::encode($model->descripcion)?></br>
                Talla: <?=Html::encode($model->talla)?></br>
                Precio: <?=Html::encode(Yii::$app->formatter->asCurrency($model->precio))?></br>
            </div>
            <div class="datos-anadir">
                <div class="numeric">
                    Cantidad: <input class="cantidad form-control" type="number" value='0' min="0" max="<?= $model->cantidad ?>"></br>
                </div>
                <div class="boton-anadir">
                    <button type="button" name="button">Añadir a carrito</button>
                </div>
            </div>
        </div>
    </div>
</div>
