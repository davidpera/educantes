<?php

namespace app\controllers;

use app\models\Alumnos;
use app\models\Tutores;
use app\models\TutoresSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * TutoresController implementa las acciones del CRUD de tutores.
 */
class TutoresController extends Controller
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
                'only' => ['index', 'create', 'update', 'view', 'upload'],
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
     * muestra todos los tutores.
     * @return mixed
     */
    public function actionIndex()
    {
        $us = Yii::$app->user->identity;
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $searchModel = new TutoresSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new Tutores();

        if ($model->load(Yii::$app->request->post())) {
            $model->colegio_id = $us->colegio_id;
            if (Alumnos::find()->where(['dni_primer_tutor' => $model->nif])->orWhere(['dni_segundo_tutor' => $model->nif])->one() === null) {
                Yii::$app->session->setFlash('error', 'El tutor que intenta introducir no tiene ningun hijo en el centro, compruebe si es un error o si no ha introducido los alumnos todavia');
                return $this->redirect(['index']);
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    /**
     * Muestra un tutor.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException si no se encuentra el tutor
     */
    public function actionView($id)
    {
        $us = Yii::$app->user->identity;
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Crea un nuevo tutor.
     * si se crea bien te manda al index.
     * @return mixed
     */
    public function actionCreate()
    {
        $us = Yii::$app->user->identity;
        if ($us->rol !== 'C') {
            return $this->goHome();
        }
        $model = new Tutores();



        if ($model->load(Yii::$app->request->post())) {
            $model->colegio_id = $us->colegio_id;
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Modifica un tutor existente.
     * si se modifica bien te manda al index.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $us = Yii::$app->user->identity;
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Borra un tutor existente.
     * si se borra bien te manda al index.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException si no encuentra el modelo
     */
    public function actionDelete($id)
    {
        $us = Yii::$app->user->identity;
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Busca un tutor segun el id dado.
     * Si no lo encuentra te da un error 404.
     * @param int $id
     * @return Tutores el modelo cargado
     * @throws NotFoundHttpException si no encuentra el modelo
     */
    protected function findModel($id)
    {
        if (($model = Tutores::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
