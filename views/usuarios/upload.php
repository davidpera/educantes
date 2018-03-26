<?php

use app\models\Alumnos;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
?>
<div>
    <h3>Introduzca un documento .xlsx con el listado de datos de su
    colegio teniendo en cuenta que la primera linea del archivo sea
    la que contiene el titulo de la columna</h3>

    <h3>El Archivo tiene que tener los siguientes campos:</h3>
    <ul>
    <?php if ($_GET['tabla'] === 'alumnos') : ?>
        <li>Unidad</li>
        <li>Nº Id. Escolar</li>
        <li>Primer Apellido</li>
        <li>Segundo Apellido</li>
        <li>Nombre</li>
        <li>Fecha de nacimiento</li>
        <li>DNI/Pasaporte Primer tutor</li>
        <li>DNI/Pasaporte Segundo tutor</li>
    <?php elseif ($_GET['tabla'] === 'libros') : ?>
        <li>ISBN</li>
        <li>Título</li>
        <li>Curso</li>
        <li>Precio</li>
    <?php else : ?>
        <li>Código</li>
        <li>Descripción</li>
        <li>Talla</li>
        <li>Precio</li>
        <li>IVA</li>
        <li>Ubicación</li>
        <li>Cantidad</li>
    <?php endif ?>
    </ul>
    <?php
    $form = ActiveForm::begin([
            'options' => [ 'enctype' => 'multipart/form-data']
    ]);
    ?>
    <?= $form->field($model, 'file_alum')->fileInput(['options' => ['accept' => 'application/vnd.ms-excel']]); ?>

    <div class="form-group">
        <?= Html::submitButton('Confirmar', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
