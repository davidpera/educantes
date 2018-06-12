<?php

namespace app\controllers;

use app\models\Productoscarro;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ProductoscarroController implementa las acciones de CRUD para los productos de los carros.
 */
class ProductoscarroController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Borra un producto existente.
     * Si se borra te manda al index.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException si no se encuentra el modelo
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Busca un producto segun el id.
     * si no se encuentra te da un error 404.
     * @param int $id
     * @return Productoscarro el modelo cargado
     * @throws NotFoundHttpException si no se encuentra el modelo
     */
    protected function findModel($id)
    {
        if (($model = Productoscarro::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
