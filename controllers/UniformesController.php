<?php

namespace app\controllers;

use app\models\Uniformes;
use app\models\UniformesSearch;
use app\models\Usuarios;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UniformesController implements the CRUD actions for Uniformes model.
 */
class UniformesController extends Controller
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
     * Lists all Uniformes models.
     * @return mixed
     * @param null|mixed $mio
     */
    public function actionIndex($mio = null)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        // if ($us->rol !== 'A' && $us->rol !== 'C') {
        //     return $this->goHome();
        // }
        $searchModel = new UniformesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $mio);

        $model = new Uniformes();

        $model->colegio_id = $us->colegio_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }
        if ($mio !== null && $mio !== 'no') {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'mio' => $mio,
                'model' => $model,
            ]);
        }
        return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
            ]);
    }

    /**
     * Displays a single Uniformes model.
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
     * Creates a new Uniformes model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'C' && $us->rol !== 'V') {
            return $this->goHome();
        }
        $model = new Uniformes();

        $model->colegio_id = $us->colegio_id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($us->rol === 'V') {
                return $this->redirect(['index', 'mio' => 'si']);
            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Uniformes model.
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
            if ($model->nif === '') {
                $model->nif = null;
            }
            if ($model->save()) {
                if ($us->rol === 'V') {
                    return $this->redirect(['index', 'mio' => 'si']);
                }
                return $this->redirect(['index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionCantidad($id)
    {
        $model = $this->findModel($id);

        if ($model->cantidad !== '0') {
            return $model->cantidad;
        }
        Yii::$app->session->setFlash('info', 'No hay existencias de ese uniforme');
        return  $this->redirect(['index', 'mio' => 'no']);
    }

    public function actionPedido($id, $cantidadPedida)
    {
        $uniform = $this->findModel($id);
        $uniform->cantidad = $uniform->cantidad - $cantidadPedida;
        if ($uniform->save()) {
            $usuario = Usuarios::find()->where(['colegio_id' => $uniform->colegio_id, 'rol' => 'V'])->one();
            $usuario->emailPedido($id, Yii::$app->user->id);
            $this->redirect(['index', 'mio' => 'no']);
        }
    }

    public function actionAceptar($id, $pedidorid)
    {
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $user->emailAceptar($id);
    }

    /**
     * Deletes an existing Uniformes model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol === 'P') {
            return $this->goHome();
        }
        $this->findModel($id)->delete();

        if ($us->rol === 'V') {
            return $this->redirect(['index', 'mio' => 'si']);
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the Uniformes model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Uniformes the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Uniformes::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
