<?php

namespace app\controllers;

use app\models\Sms;
use app\models\SmsSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * SmsController implementa las acciones del CRUD de sms.
 */
class SmsController extends Controller
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
                'only' => ['index'],
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
     * Muestra todos los sms del usuario actual.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->identity->rol !== 'V') {
            return $this->goHome();
        }
        $searchModel = new SmsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
