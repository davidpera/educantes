<?php

use yii\helpers\Html;

use yii\widgets\ListView;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Colegios */

$this->title = 'Gestionar Colegios';
$this->params['breadcrumbs'][] = ['label' => 'Colegios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="colegios-gestionar">
    <h2 class="titulo">Gestion de colegios</h2>
    <?= ListView::widget([
       'options' => [
           'tag' => 'div',
           'class' => 'panel-group col-md-3'
       ],
       'dataProvider' => $dataProvider,
       'itemView' => function ($model, $key, $index, $widget) {
           $itemContent = $this->render('_colegio',['model' => $model]);

           return $itemContent;
       },
       'itemOptions' => [
           'tag' => false,
       ],
       'summary' => '',

       'layout' => '{items}{pager}',

       'pager' => [
           'maxButtonCount' => 3,
           'options' => [
               'class' => 'pagination col-xs-12'
           ]
       ],

   ]);
   ?>

   <div class="sucursales-form col-md-offset-2 col-md-3">

       <?php $form = ActiveForm::begin(['options' => ['id' => 'gestionar-form']]); ?>

       <?= $form->field($model, 'cif')->textInput(['maxlength' => 9]) ?>

       <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

       <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

       <?= $form->field($model, 'cod_postal')->textInput(['maxlength' => 5]) ?>

       <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

       <div class="form-group">
           <?= Html::a('Cancelar', ['site/index'], ['class' => 'btn btn-danger']) ?>
           <?= Html::submitButton('Guardar',['class' => 'btn btn-success']) ?>
           <?= Html::a('',['gestionar'],['class' => 'btn glyphicon glyphicon-plus']) ?>
       </div>

       <?php ActiveForm::end(); ?>

   </div>

</div>
