<?php

namespace app\controllers;

use app\models\Alumnos;
use app\models\AlumnosSearch;
use app\models\Tutores;
use app\models\Usuarios;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AlumnosController implements the CRUD actions for Alumnos model.
 */
class AlumnosController extends Controller
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
                'only' => ['index', 'create', 'update', 'view'],
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
     * Lists all Alumnos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $searchModel = new AlumnosSearch();
        $dataProvider = $searchModel
        ->search(Yii::$app->request->queryParams);

        $model = new Alumnos();
        $model->colegio_id = $us->colegio_id;

        if ($model->load(Yii::$app->request->post())) {
            $tutor1 = Tutores::find()->where(['nif' => $model->dni_primer_tutor])->one();
            if ($tutor1 === null) {
                Yii::$app->session->setFlash('error', 'El alumno que intenta crear no tiene un tutor creado todavia, cree primero los tutores y luego los alumnos');
                return $this->redirect(['index']);
            }
            $model->tutor_id = $tutor1->id;
            $tutor2 = Tutores::find()->where(['nif' => $model->dni_segundo_tutor])->one();
            if ($tutor2 !== null) {
                $model->tutor2_id = $tutor2->id;
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
     * Displays a single Alumnos model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Alumnos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'C') {
            return $this->goHome();
        }
        $model = new Alumnos();
        $model->colegio_id = $us->colegio_id;

        if ($model->load(Yii::$app->request->post())) {
            $tutor1 = Tutores::find()->where(['nif' => $model->dni_primer_tutor])->one();
            if ($tutor1 === null) {
                Yii::$app->session->setFlash('error', 'El alumno que intenta crear no tiene un tutor creado todavia');
                return $this->redirect(['index']);
            }
            $model->tutor_id = $tutor1->id;
            $tutor2 = Tutores::find()->where(['nif' => $model->dni_segundo_tutor])->one();
            if ($tutor2 !== null) {
                $model->tutor2_id = $tutor2->id;
            }
            // if ($model->save()) {
            //     return $this->redirect(['index']);
            // }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Alumnos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->segundo_apellido === '') {
                $model->segundo_apellido = null;
            }
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Alumnos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Alumnos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Alumnos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Alumnos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
