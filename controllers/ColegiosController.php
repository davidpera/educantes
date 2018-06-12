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
 * ColegiosController implementa las acciones del CRUD para Colegios.
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
     * Devuelve todos los colegios a los que el usuario no pertenezca.
     * @return array Conjunto de colegios
     */
    public function actionLista()
    {
        $lista = Colegios::find()->where('id != :id', ['id' => Yii::$app->user->identity->colegio_id])->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $lista;
    }

    /**
     * Muestra todos los modelos de colegio.
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
     * Muestra un solo colegio.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException si no es encontrado el colegio
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
     * Borra un colegio existente.
     * Si el borrado funciona correctamente se redireccionara al usuario a la pagina de index.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['gestionar']);
    }

    /**
     * Busca un colegio segun el id recibido.
     * Si no se encuentra el colegio manda un error 404.
     * @param int $id
     * @return Colegios el modelo cargado
     * @throws NotFoundHttpException si el modelo no es encontrado
     */
    protected function findModel($id)
    {
        if (($model = Colegios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
