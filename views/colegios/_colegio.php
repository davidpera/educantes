<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<div class="panel panel-default">
    <div class="panel panel-heading contenedor">
        <?= $model->nombre ?>
        <div>
            <?= Html::a('', ['colegios/gestionar', 'id' => $model->id],['class' => 'btn glyphicon glyphicon-pencil'])?>
            <?= Html::a('', ['colegios/delete', 'id' => $model->id],[
                'class' => 'btn glyphicon glyphicon-trash',
                'data' => [
                    'confirm' => 'Seguro que quieres borrar el colegio '.$model->nombre,
                    'method' => 'post',
                ],
                ])?>
        </div>

    </div>
</div>
