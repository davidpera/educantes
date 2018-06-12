<?php

namespace app\controllers;

use app\models\Secstocks;
use app\models\Usuarios;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * SecstocksController implementa las acciones del crud para stocks de seguridad.
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
                'only' => ['create', 'update'],
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
     * Crea un nuevo stock de seguridad.
     * si la creacion es exitosa te manda al index.
     * @return mixed
     * @param mixed $uniforme_id
     */
    public function actionCreate($uniforme_id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'C' && $us->rol !== 'V') {
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
     * Modifica un stock de seguridad existente.
     * si la modificacion es exitosa te manda al index.
     * @param mixed $uniforme_id
     * @return mixed
     * @throws NotFoundHttpException si no encuentra el modelo
     */
    public function actionUpdate($uniforme_id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        if ($us->rol !== 'C' && $us->rol !== 'V') {
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
     * Busca un stock de seguridad segun el id recibido.
     * Si no lo encuentra manda un error 404.
     * @param int $id
     * @return Secstocks el modelo cargado
     * @throws NotFoundHttpException si no encuentra el modelo
     */
    protected function findModel($id)
    {
        if (($model = Secstocks::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
