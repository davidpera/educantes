<?php

namespace app\controllers;

use app\models\Carros;
use app\models\Colegios;
use app\models\Productoscarro;
use app\models\Secstocks;
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
                'only' => ['create', 'update', 'view', 'index'],
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
     * Añade un uniforme al carro del usuario padre actual.
     */
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
                $vend = Usuarios::findOne(['colegio_id' => $uniforme->colegio_id, 'rol' => 'V']);
                if ($vend !== null) {
                    if ($vend->tel_movil !== null) {
                        $vend->smsStock($uniforme->id);
                    }
                }
                // var_dump($prodcar->cantidad);
                // die();
                if ($uniforme->save()) {
                    $carr = Carros::findOne(['id' => $prodcar->carro_id]);
                    $carr->productos = $carr->productos + 1;
                    $carr->save();
                }
            }
        }
        // return true;
    }

    /**
     * Quita un uniforme del carro del usuario padre actual.
     */
    public function actionQuitar()
    {
        $producto = Productoscarro::findOne(['id' => $_POST['producto']]);
        $uniforme = Uniformes::findOne(['id' => $producto->uniforme_id]);
        $cantidad = $producto->cantidad;
        if ($producto->delete()) {
            $uniforme->cantidad = $uniforme->cantidad + $cantidad;
            $secs = Secstocks::findOne(['uniforme_id' => $uniforme->id]);
            if ($secs !== null && $uniforme->cantidad > $secs->mp) {
                $uniforme->underss = true;
                $uniforme->save();
            }
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

    /**
     * Devuelve la cantidad que hay del uniforme marcado.
     * @param  int $id Id del uniforme del cual se quiere saver la cantidad
     * @return int     Cantidad de uniformes
     */
    public function actionCantidad($id)
    {
        $model = $this->findModel($id);

        if ($model->cantidad !== '0') {
            return $model->cantidad;
        }
        Yii::$app->session->setFlash('info', 'No hay existencias de ese uniforme');
        return  $this->redirect(['index']);
    }

    /**
     * Nos devuelve todos los uniformes que no pertenecen al colegio del usuario.
     * @return array Grupo de uniformes
     */
    public function actionExternos()
    {
        $colegio = Colegios::findOne(['nombre' => $_GET['nombre']]);
        $externos = Uniformes::find()->where(['colegio_id' => $colegio->id])->all();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $externos;
    }

    /**
     * Pedido simple realizado a un colegio.
     * @param  int $id             Id del uniforme
     * @param  int $cantidadPedida Cantidad de uniformes de ese tipo pedidos
     */
    public function actionPedido($id, $cantidadPedida)
    {
        $uniform = $this->findModel($id);
        $uniform->cantidad = $uniform->cantidad - $cantidadPedida;
        $vend = Usuarios::findOne(['colegio_id' => $uniform->colegio_id, 'rol' => 'V']);
        if ($vend !== null) {
            if ($vend->tel_movil !== null) {
                $vend->smsStock($uniform->id);
            }
        }
        if ($uniform->save()) {
            $usuario = Usuarios::find()->where(['colegio_id' => $uniform->colegio_id, 'rol' => 'V'])->one();
            $usuario->emailPedido($id, Yii::$app->user->id, $cantidadPedida);
            Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al administrador del colegio, se le contestará cuando lo acepte');
            // return $this->redirect(['index']);
        }
    }

    /**
     * Pedido multiple realizado a varios colegios con varios uniformes.
     * @param  array $pedido    Grupo de colegios con los uniformes que se les pide
     *                          a cada uno y su cantidad
     */
    public function actionMultiple($pedido)
    {
        // var_dump($pedido);
        if ($pedido) {
            $com = json_decode($pedido);
            // $pedidos = [];
            $pasa = true;
            // var_dump($com);
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
                            $vend = Usuarios::findOne(['colegio_id' => $uniform->colegio_id, 'rol' => 'V']);
                            if ($vend !== null) {
                                if ($vend->tel_movil !== null) {
                                    $vend->smsStock($uniform->id);
                                    // die();
                                }
                            }
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

    /**
     * Aceptacion del pedido simple de uniforme.
     * @param  int $id        Id del uniforme
     * @param  int $pedidorid Id del usuario que realizo el pedido
     */
    public function actionAceptar($id, $pedidorid)
    {
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $user->emailAceptar($id);
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario para que venga a recoger el pedido');
        $this->goHome();
    }

    /**
     * Rechazo del pedido simple de uniforme.
     * @param  int $id             Id del uniforme
     * @param  int $pedidorid      Id del usuario que realizo el pedido
     * @param  int $cantidadPedida Cantidad pedida de ese uniforme
     */
    public function actionRechazar($id, $pedidorid, $cantidadPedida)
    {
        $uniform = $this->findModel($id);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $uniform->cantidad = $uniform->cantidad + $cantidadPedida;
        $secs = Secstocks::findOne(['uniforme_id' => $uniform->id]);
        if ($secs !== null && $uniform->cantidad > $secs->mp) {
            $uniform->underss = true;
            $uniform->save();
        }
        if ($uniform->save()) {
            $user->emailRechazar($id);
            Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario informandole que el pedido ha sido rechazado');
            $this->goHome();
        }
    }

    /**
     * Aceptacion de la parte del pedido multiple de uniformes a tu colegio.
     * @param  array  $articulos Uniformes pedidos
     * @param  int    $pedidorid Id del usuario que ha realizado el pedido
     * @param  int    $recibidor Id del usuario que ha recivido el pedido
     */
    public function actionAceptarmul($articulos, $pedidorid, $recibidor)
    {
        $rec = Usuarios::findOne(['id' => $recibidor]);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $user->emailAceptarmul($rec->colegio_id);
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario para que venga a recoger el pedido');
        $this->goHome();
    }

    /**
     * Rechazo del pedido multiple de uniformes.
     * @param  [type] $articulos Uniformes pedidos
     * @param  int    $pedidorid Id del usuario que ha realizado el pedido
     * @param  int    $recibidor Id del usuario que ha recivido el pedido
     */
    public function actionRechazarmul($articulos, $pedidorid, $recibidor)
    {
        $rec = Usuarios::findOne(['id' => $recibidor]);
        $user = Usuarios::find()->where(['id' => $pedidorid])->one();
        $valetodos = true;
        $artic = json_decode($articulos);
        foreach ($artic as $art) {
            $uniform = Uniformes::findOne(['id' => $art[0]]);
            $uniform->cantidad = $uniform->cantidad + $art[1];
            $secs = Secstocks::findOne(['uniforme_id' => $uniform->id]);
            if ($secs !== null && $uniform->cantidad > $secs->mp) {
                $uniform->underss = true;
                $uniform->save();
            }
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
