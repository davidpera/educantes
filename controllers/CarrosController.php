<?php

namespace app\controllers;

use app\models\Carros;
use app\models\CarrosSearch;
use app\models\Productoscarro;
use app\models\Usuarios;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * CarrosController implementa las acciones de CRUD para los carros.
 */
class CarrosController extends Controller
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
     * Muesta una lista de los productos del carro del padre actual.
     * @return mixed
     */
    public function actionCarrito()
    {
        if (Yii::$app->user->identity->rol !== 'P') {
            return $this->redirect(['/site/index']);
        }
        $query = Productoscarro::find()->where(['carro_id' => Yii::$app->user->identity->carro->id, 'realizado' => false]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('/carros/view', [
                'dataProvider' => $dataProvider,
            ]);
    }

    /**
     * Muestra una lsita de los productos que ya se han pedido.
     * @return mixed
     */
    public function actionRealizados()
    {
        if (Yii::$app->user->identity->rol !== 'P') {
            return $this->redirect(['/site/index']);
        }

        $pedidos = true;
        $query = Productoscarro::find()->where(['carro_id' => Yii::$app->user->identity->carro->id, 'realizado' => true]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('/carros/view', [
                'dataProvider' => $dataProvider,
                'pedidos' => $pedidos,
            ]);
    }

    /**
     * Realiza un pedido de uniformes pasados por post al colegio de estos.
     */
    public function actionPedido()
    {
        $com = json_decode($_POST['pedido']);
        foreach ($com->pedidos as $ped) {
            // var_dump($ped);
            // die();
            $mode = Productoscarro::findOne(['id' => $ped[5]]);
            $mode->realizado = true;
            $mode->fecha_pedido = date('Y-m-d H:i:s');
            // var_dump(Yii::$app->formatter->asDatetime($mode->fecha_pedido));
            // die();
            $mode->save();
        }
        $carro = Carros::findOne(['usuario_id' => Yii::$app->user->id]);
        $carro->productos = 0;
        $carro->save();
        // var_dump(json_decode($_POST['pedido'])->pedidos[0]);
        // die();
        $usuario = Usuarios::find()->where(['colegio_id' => Yii::$app->user->identity->colegio_id, 'rol' => 'V'])->one();
        if (!isset($usuario)) {
            $usuario = Usuarios::find()->where(['colegio_id' => Yii::$app->user->identity->colegio_id, 'rol' => 'C'])->one();
        }
        $usuario->emailPedidoPadre(Yii::$app->user->id, $com->pedidos);
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al administrador del colegio, se le contestará cuando lo acepte');
        return $this->redirect(['/site/index']);
    }

    /**
     * Acepta el pedido que le ha llegado por correo.
     * @param  array $pedido      Conjunto de uniformes que se han pedido
     * @param  int   $pedidorid   Id del usuario que ha realizado el pedido
     */
    public function actionAceptar($pedido, $pedidorid)
    {
        $com = json_decode($pedido);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $user->emailAceptarPadre($com);
        foreach ($com as $ped) {
            $mode = Productoscarro::findOne(['id' => $ped[5]]);
            $mode->aceptado = true;
            $mode->save();
        }
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario para que venga a recoger el pedido');
        $this->goHome();
    }

    /**
     * Lista todos los carros.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CarrosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Carros model.
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
     * Creates a new Carros model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Carros();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Carros model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Carros model.
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
     * Finds the Carros model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Carros the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Carros::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
