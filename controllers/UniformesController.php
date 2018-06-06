<?php

namespace app\controllers;

use app\models\Carros;
use app\models\Colegios;
use app\models\Productoscarro;
use app\models\Uniformes;
use app\models\UniformesSearch;
use app\models\Usuarios;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

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

    public function actionAnadir()
    {
        $uniforme = Uniformes::findOne(['id' => $_POST['uniforme']]);
        $prodcar = new Productoscarro();
        $prodcar->carro_id = Yii::$app->user->identity->carro->id;
        $prodcar->uniforme_id = $uniforme->id;
        $prodcar->cantidad = $_POST['cantidad'];
        if ($_POST['cantidad'] !== '0') {
            if ($prodcar->save()) {
                $uniforme->cantidad = $uniforme->cantidad - $prodcar->cantidad;
                if ($uniforme->save()) {
                    $carr = Carros::findOne(['id' => $prodcar->carro_id]);
                    $carr->productos = $carr->productos + 1;
                    $carr->save();
                }
            }
        }
        // return true;
    }

    public function actionQuitar()
    {
        $producto = Productoscarro::findOne(['id' => $_POST['producto']]);
        $uniforme = Uniformes::findOne(['id' => $producto->uniforme_id]);
        $cantidad = $producto->cantidad;
        if ($producto->delete()) {
            $uniforme->cantidad = $uniforme->cantidad + $cantidad;
            if ($uniforme->save()) {
                $carr = Carros::findOne(['id' => $producto->carro_id]);
                $carr->productos = $carr->productos - 1;
                $carr->save();
            }
        }
        // return true;
    }

    /**
     * Lists all Uniformes models.
     * @return mixed
     */
    public function actionIndex()
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        // if ($us->rol !== 'A' && $us->rol !== 'C') {
        //     return $this->goHome();
        // }
        $searchModel = new UniformesSearch();
        $mioProvider = $searchModel->search(Yii::$app->request->queryParams, 'si');
        $otroProvider = $searchModel->search(Yii::$app->request->queryParams, 'no');

        $model = new Uniformes();

        $model->colegio_id = $us->colegio_id;
        if ($model->load(Yii::$app->request->post())) {
            $model->foto = UploadedFile::getInstance($model, 'foto');
            if ($model->save() && $model->upload()) {
                return $this->goBack();
            }
        }
        return $this->render('index', [
                'searchModel' => $searchModel,
                'mioProvider' => $mioProvider,
                'otroProvider' => $otroProvider,
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
        if ($model->load(Yii::$app->request->post())) {
            $model->foto = UploadedFile::getInstance($model, 'foto');
            if ($model->save() && $model->upload()) {
                if ($us->rol === 'V') {
                    return $this->redirect(['index']);
                }
                return $this->redirect(['index']);
            }
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
        if ($us->rol !== 'A' && $us->rol !== 'C' && $us->rol !== 'V') {
            return $this->goHome();
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            // if ($model->nif === '') {
            //     $model->nif = null;
            // }
            $model->foto = UploadedFile::getInstance($model, 'foto');
            if ($model->save() && $model->upload()) {
                if ($us->rol === 'V') {
                    return $this->redirect(['index']);
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
        return  $this->redirect(['index']);
    }

    public function actionExternos()
    {
        // var_dump($_GET['nombre']);
        // die();
        $colegio = Colegios::findOne(['nombre' => $_GET['nombre']]);
        $externos = Uniformes::find()->where(['colegio_id' => $colegio->id])->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $externos;
    }

    public function actionPedido($id, $cantidadPedida)
    {
        $uniform = $this->findModel($id);
        $uniform->cantidad = $uniform->cantidad - $cantidadPedida;
        if ($uniform->save()) {
            $usuario = Usuarios::find()->where(['colegio_id' => $uniform->colegio_id, 'rol' => 'V'])->one();
            $usuario->emailPedido($id, Yii::$app->user->id, $cantidadPedida);
            Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al administrador del colegio, se le contestará cuando lo acepte');
            $this->redirect(['index']);
        }
    }

    public function actionMultiple($pedido)
    {
        if ($pedido) {
            $com = json_decode($pedido);
            // $pedidos = [];
            $pasa = true;
            foreach ($com->pedidos as $ped) {
                $colegio = Colegios::find()->where(['nombre' => $ped[0]])->one();
                $us = Usuarios::find()->where(['colegio_id' => $colegio->id, 'rol' => 'V'])->one();
                $pedid = [];
                for ($i = 1; $i < count($ped); $i++) {
                    foreach ($ped[$i] as $un) {
                        $uniform = $this->findModel($un[0]);
                        if ($un[1] > $uniform->cantidad) {
                            $pasa = false;
                        } else {
                            $uniform->cantidad = $uniform->cantidad - $un[1];
                            $uniform->save();
                            $pedid[] = [$un[0], $un[1]];
                        }
                    }
                }
                if ($pasa) {
                    $us->emailMultiple($pedid, Yii::$app->user->id);
                }
                // $pedidos[] = $pedid;
                // var_dump($ped);
            }
            if (!$pasa) {
                Yii::$app->session->setFlash('error', 'Ha puesto demasiada cantidad en algunos de los uniformes, revise los datos');
                return true;
            }
            // var_dump($pedidos);
            // die();
            // Yii::$app->session->setFlash('info', 'Se le ha enviado un correo a los administradores de las colegios, se le contestará cuando lo acepte');
            // $this->redirect(['index', 'mio' => 'no']);
        }
    }

    public function actionAceptar($id, $pedidorid)
    {
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $user->emailAceptar($id);
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario para que venga a recoger el pedido');
        $this->goHome();
    }

    public function actionRechazar($id, $pedidorid, $cantidadPedida)
    {
        $uniform = $this->findModel($id);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $uniform->cantidad = $uniform->cantidad + $cantidadPedida;
        if ($uniform->save()) {
            $user->emailRechazar($id);
            Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario informandole que el pedido ha sido rechazado');
            $this->goHome();
        }
    }

    public function actionAceptarmul($articulos, $pedidorid, $recibidor)
    {
        $rec = Usuarios::findOne(['id' => $recibidor]);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $user->emailAceptarmul($rec->colegio_id);
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario para que venga a recoger el pedido');
        $this->goHome();
    }

    public function actionRechazarmul($articulos, $pedidorid, $recibidor)
    {
        $rec = Usuarios::findOne(['id' => $recibidor]);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $valetodos = true;
        $artic = json_decode($articulos);
        foreach ($artic as $art) {
            $uniform = Uniformes::findOne(['id' => $art[0]]);
            $uniform->cantidad = $uniform->cantidad + $art[1];
            if (!$uniform->save()) {
                $valetodos = false;
            }
        }

        if ($valetodos) {
            $user->emailRechazarmul($rec->colegio_id);
            Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario informandole que el pedido ha sido rechazado');
            $this->goHome();
        }
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
            return $this->redirect(['index']);
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
