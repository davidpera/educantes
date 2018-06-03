<?php

use yii\helpers\Html;


?>


<div id='<?= $model->id ?>' class="panel panel-default">
    <div class="panel panel-heading contenedor">
        <div class="imagen"><img src="<?= $model->getRutaImagen() ?>"></div>
        <div class="informacion"><?= Html::encode($model->descripcion)?></br>
            Talla: <?=Html::encode($model->talla)?></br>
            Cantidad: <input class="cantidad" type="number" value='0' min="0" max="<?= $model->cantidad ?>"></br>
            <div class="boton-anadir">
                <button type="button" name="button">Añadir a carrito</button>
            </div>
        </div>
    </div>
</div>
