<?php

namespace app\controllers;

use app\models\Colegios;
use app\models\ColegiosSearch;
use app\models\Usuarios;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ColegiosController implements the CRUD actions for Colegios model.
 */
class ColegiosController extends Controller
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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'create', 'update', 'view', 'gestionar'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Devuleve todos los colegios a los que el usuario no pertenezca.
     * @return array Conjunto de colegios
     */
    public function actionLista()
    {
        $lista = Colegios::find()->where('id != :id', ['id' => Yii::$app->user->identity->colegio_id])->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }

    /**
     * Lists all Colegios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $searchModel = new ColegiosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Colegios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Muestra la pagina de gestion de colegios en la cual puedes añadir colegios
     * nuevos y modificar existentes.
     * @param  int  $id     Id del colegio que se quiere modificar
     * @return mixed
     */
    public function actionGestionar($id = null)
    {
        $query = Colegios::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 7,
            ],
            'sort' => ['defaultOrder' => ['nombre' => SORT_ASC]],
        ]);

        if ($id === null) {
            $model = new Colegios();
        } else {
            $model = $this->findModel($id);
        }



        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['gestionar']);
        }

        return $this->render('gestionar', [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Colegios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Colegios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Colegios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Colegios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
