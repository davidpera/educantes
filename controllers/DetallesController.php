<?php

namespace app\controllers;

use Yii;
use app\models\Detalles;
use app\models\DetallesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DetallesController implements the CRUD actions for Detalles model.
 */
class DetallesController extends Controller
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
     * Lists all Detalles models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DetallesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Detalles model.
     * @param integer $num_detalle
     * @param integer $factura_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($num_detalle, $factura_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($num_detalle, $factura_id),
        ]);
    }

    /**
     * Creates a new Detalles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Detalles();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'num_detalle' => $model->num_detalle, 'factura_id' => $model->factura_id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Detalles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $num_detalle
     * @param integer $factura_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($num_detalle, $factura_id)
    {
        $model = $this->findModel($num_detalle, $factura_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'num_detalle' => $model->num_detalle, 'factura_id' => $model->factura_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Detalles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $num_detalle
     * @param integer $factura_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($num_detalle, $factura_id)
    {
        $this->findModel($num_detalle, $factura_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Detalles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $num_detalle
     * @param integer $factura_id
     * @return Detalles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($num_detalle, $factura_id)
    {
        if (($model = Detalles::findOne(['num_detalle' => $num_detalle, 'factura_id' => $factura_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
