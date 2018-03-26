<?php

namespace app\controllers;

use app\models\Secstocks;
use app\models\SecstocksSearch;
use app\models\Usuarios;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * SecstocksController implements the CRUD actions for Secstocks model.
 */
class SecstocksController extends Controller
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
     * Lists all Secstocks models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SecstocksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Secstocks model.
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
     * Creates a new Secstocks model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @param mixed $uniforme_id
     */
    public function actionCreate($uniforme_id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'C') {
            return $this->goHome();
        }
        $model = new Secstocks();
        $model->uniforme_id = $uniforme_id;

        if ($model->load(Yii::$app->request->post())) {
            $mp = ($model->cd * $model->pe) + $model->ss;
            $model->mp = $mp;
            if ($model->save()) {
                return $this->redirect(['uniformes/index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Secstocks model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param mixed $uniforme_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($uniforme_id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'C') {
            return $this->goHome();
        }
        $st = Secstocks::find()->where(['uniforme_id' => $uniforme_id])->one();
        $model = $this->findModel($st->id);

        if ($model->load(Yii::$app->request->post())) {
            $mp = ($model->cd * $model->pe) + $model->ss;
            $model->mp = $mp;
            if ($model->save()) {
                return $this->redirect(['uniformes/index']);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Secstocks model.
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
     * Finds the Secstocks model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Secstocks the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Secstocks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
