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
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['index', 'create', 'update', 'view', 'carrito', 'realizados'],
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
     * Muestra una lista de los productos que ya se han pedido.
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
     * Mustra un carro con todos sus datos.
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
     * Crea un nuevo carro.
     * Si se crea bien te devuelve a la vista de carros.
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
     * Modifica un carro existente.
     * Si se modifica bien te devuelve a la vista de carros.
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
     * Borra un carro existente.
     * Si se borra bien te devuelve al index de carros.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException si no es encontrado el carro
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Busca un carro segun el id recibido.
     * Si no se encuentra el carro manda un error 404.
     * @param int $id
     * @return Carros el modelo cargado
     * @throws NotFoundHttpException si el modelo no es encotrado
     */
    protected function findModel($id)
    {
        if (($model = Carros::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
