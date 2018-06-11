<?php

namespace app\controllers;

use app\models\Alumnos;
use app\models\Carros;
use app\models\Libros;
use app\models\Tutores;
use app\models\Uniformes;
use app\models\UploadForm;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
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
                'only' => ['index', 'create', 'update', 'view', 'upload', 'alta'],
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
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $us = Yii::$app->user->identity;
        if ($us->rol !== 'A' && $us->rol !== 'C') {
            return $this->goHome();
        }
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Este método descarga el archivo y lo inyecta directamente
     * a la base de datos.
     * @param  string $tabla A que tabla se van a inyectar los datos
     */
    public function actionUpload($tabla)
    {
        if (Yii::$app->user->identity->rol === 'C') {
            $model = new UploadForm();

            if (Yii::$app->request->isPost) {
                $model->file_alum = UploadedFile::getInstance($model, 'file_alum');
                if ($model->upload()) {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load('uploads/' . $model->file_alum);

                    $worksheet = $spreadsheet->getActiveSheet();
                    // Get the highest row number and column letter referenced in the worksheet
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    // Increment the highest column letter
                    $highestColumn++;

                    $connection = Yii::$app->db;
                    $sql = "SELECT column_name
                            FROM information_schema.columns
                            WHERE table_schema = 'public'
                            AND table_name = '$tabla'";
                    $command = $connection->createCommand($sql);
                    $dataReader = $command->query();
                    $rows = $dataReader->readAll();

                    for ($row = 2; $row <= $highestRow; ++$row) {
                        if ($tabla === 'alumnos') {
                            $model = new Alumnos();
                        } elseif ($tabla === 'libros') {
                            $model = new Libros();
                        } elseif ($tabla === 'uniformes') {
                            $model = new Uniformes();
                        } else {
                            $model = new Tutores();
                        }
                        $model->colegio_id = Yii::$app->user->identity->colegio_id;

                        for ($col = 'A'; $col != $highestColumn; ++$col) {
                            $celda = $worksheet->getCell($col . $row)
                                ->getValue();
                            if ($tabla === 'alumnos') {
                                switch ($worksheet->getCell($col . 1)
                                    ->getValue()) {
                                    case 'Nº Id. Escolar':
                                        $worksheet->getCell($col . 1)
                                        ->setValue('codigo');
                                        break;
                                    case 'DNI/Pasaporte Primer tutor':
                                        $worksheet->getCell($col . 1)
                                        ->setValue('dni_primer_tutor');
                                        break;
                                    case 'DNI/Pasaporte Segundo tutor':
                                        $worksheet->getCell($col . 1)
                                        ->setValue('dni_segundo_tutor');
                                        break;
                                    case 'Primer Apellido':
                                        $worksheet->getCell($col . 1)
                                        ->setValue('primer_apellido');
                                        break;
                                    case 'Segundo Apellido':
                                        $worksheet->getCell($col . 1)
                                        ->setValue('segundo_apellido');
                                        break;
                                    case 'Fecha de nacimiento':
                                        $worksheet->getCell($col . 1)
                                        ->setValue('fecha_de_nacimiento');
                                        break;
                                }
                            }
                            $campo = strtolower($worksheet->getCell($col . 1)
                                ->getValue());
                            $cade = utf8_decode($campo);
                            $no_permitidas = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É',
                            'Í', 'Ó', 'Ú', 'ñ', 'À', 'Ã', 'Ì', 'Ò', 'Ù', 'Ã™', 'Ã',
                            'Ã¨', 'Ã¬', 'Ã²', 'Ã¹', 'ç', 'Ç', 'Ã¢', 'ê', 'Ã®', 'Ã´',
                            'Ã»', 'Ã‚', 'ÃŠ', 'ÃŽ', 'Ã', 'Ã›', 'ü', 'Ã¶', 'Ã–', 'Ã¯',
                            'Ã¤', '«', 'Ò', 'Ã', 'Ã„', 'Ã‹', 'Ñ', 'à', 'è', 'ì', 'ò', 'ù', ];
                            $permitidas = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U',
                            'n', 'N', 'A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u', 'c',
                            'C', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'u', 'o',
                            'O', 'i', 'a', 'e', 'U', 'I', 'A', 'E', 'N', 'a', 'e', 'i', 'o', 'u', ];
                            $texto = str_replace($no_permitidas, $permitidas, $cade);

                            $worksheet->getCell($col . 1)->setValue($texto);
                            $vale = false;
                            for ($i = 0; $i < count($rows); $i++) {
                                if ($rows[$i]['column_name'] === $texto) {
                                    // var_dump($texto);
                                    $vale = true;
                                }
                            }

                            if ($vale === false) {
                                Yii::$app->session->setFlash('error', 'Campos del archivo incorrectos, revise el archivo');
                                return $this->redirect(['upload', 'tabla' => $tabla]);
                            }

                            if ($texto == 'fecha_de_nacimiento') {
                                $celda = str_replace('/', '-', $celda);
                                // var_dump($celda);
                            }

                            $model->$texto = $celda;
                            if ($tabla === 'tutores') {
                                if ($texto === 'nif' && Alumnos::find()->where(['dni_primer_tutor' => $model->nif])->orWhere(['dni_segundo_tutor' => $model->nif])->one() === null) {
                                    Yii::$app->session->setFlash('error', 'Algun de los tutores insertados no tiene nigun hijo en el colegio, compruebe si es un error o si no ha introducido los alumnos todavia');
                                    return $this->redirect(['upload', 'tabla' => $tabla]);
                                }
                            }
                        }
                        if ($tabla === 'alumnos') {
                            $un = Alumnos::findOne(['codigo' => $model->codigo]);
                            if ($un === null) {
                                $model->save();
                            }
                        } elseif ($tabla === 'libros') {
                            $model->curso = '' . $model->curso;
                            $un = Libros::findOne(['isbn' => $model->isbn]);
                            if ($un === null) {
                                // var_dump($model->validate(), $model->errors);
                                // die();
                                $model->save();
                            }
                        } elseif ($tabla === 'uniformes') {
                            $model->codigo = '' . $model->codigo;
                            $un = Uniformes::findOne(['codigo' => $model->codigo]);
                            if ($un !== null) {
                                $un->cantidad = $un->cantidad + $model->cantidad;
                                $un->save();
                            } else {
                                $model->save();
                            }
                        } else {
                            $un = Tutores::findOne(['nif' => $model->nif]);
                            if ($un === null) {
                                $model->save();
                            }
                        }
                        // die();
                        // var_dump($model->fecha_de_nacimiento);
                        // var_dump($model->validate(), $model->errors);
                        // die();
                    }
                    if ($tabla === 'alumnos') {
                        return $this->redirect(['alumnos/index']);
                    } elseif ($tabla === 'libros') {
                        return $this->redirect(['libros/index']);
                    } elseif ($tabla === 'uniformes') {
                        return $this->redirect(['uniformes/index']);
                    }
                    return $this->redirect(['tutores/index']);
                }
            }

            return $this->render('upload', ['model' => $model]);
        }
        return $this->goHome();
    }

    /**
     * Displays a single Usuarios model.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $us = Yii::$app->user->identity;
        $visto = Usuarios::find()->where(['id' => $id])->one();
        if ($us->rol === 'A' || ($us->rol === 'C' && $us->colegio_id === $visto->colegio_id)) {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
        return $this->goHome();
    }

    public function actionVerificar($token_val)
    {
        $model = Usuarios::findOne(['token_val' => $token_val]);
        if ($model === null) {
            Yii::$app->session->setFlash('error', 'Usuario ya validado');
        }
        $model->token_val = null;
        $model->save();
        Yii::$app->session->setFlash('success', 'Usuario validado. Logeese');
        return $this->redirect(['site/login']);
    }

    /**
     * Accion usda para cambiar la contraseña una vez que se ha pedido devido
     * a que el usuario la ha olvidado.
     * @param  string $token_val Token que nos dice que el usuario a pedido cambiar la contraseña
     */
    public function actionCambiar($token_val)
    {
        $us = Usuarios::findOne(['token_val' => $token_val]);
        if ($us !== null) {
            $us->scenario = Usuarios::ESCENARIO_CAMBIO;
            $us->password = '';
            $us->token_val = null;
            if ($us->load(Yii::$app->request->post()) && $us->save()) {
                return $this->redirect('/site/index');
            }
            Yii::$app->session->setFlash('success', 'Correo para cambiar contraseña enviado.');
            return $this->render('contraseña', ['model' => $us]);
        }
        return $this->redirect('/site/index');
    }

    /**
     * Accion que manda el correo para cuando un usaurio a olvidado su contraseña
     * una vez halla metido en correo en un formulario.
     * @return mixed
     */
    public function actionOlvidado()
    {
        $model = new Usuarios();
        if (Yii::$app->request->post()) {
            $us = Usuarios::findOne(['email' => Yii::$app->request->post()['Usuarios']['email']]);
            if ($us !== null) {
                $us->token_val = Yii::$app->security->generateRandomString(18);
                if ($us->save()) {
                    Yii::$app->session->setFlash('success', 'Correo para la recuperacion de contraseña enviado');
                    $us->emailRecuperacion($us->token_val);
                    return $this->redirect('/site/index');
                }
            }
            Yii::$app->session->setFlash('error', 'El correo indicado no esta registrado');
            return $this->redirect('/site/index');
        }
        return $this->render('olvidado', [
            'model' => $model,
        ]);
    }

    /**
     * Accion que carga el formulario que el usuario dado de alta tiene que rellenar
     * para poder logearse en la pagina.
     * @param  int $id Id del usuario
     * @return mixed
     */
    public function actionRegistro($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'create';
        $model->password = '';

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                return $this->goHome();
            }
        }

        return $this->render('registro', [
            'model' => $model,
        ]);
    }

    /**
     * Este método da de alta a un usuario nuevo metiendo su email y mandandole un mensaje,
     * o si es unpadre introduciendo todos los datos de este.
     * @param  int $colegio_id Id del colegio del usuario que esta dando de alta al nuevo
     * @param null|mixed $idtut
     */
    public function actionAlta($colegio_id, $idtut = null)
    {
        if ($idtut !== null) {
            $tutor = Tutores::find()->where(['id' => $idtut])->one();
            $model = new Usuarios(['scenario' => Usuarios::ESCENARIO_CREATE]);
            $model->nom_usuario = substr($tutor->nombre, 0, 2) . substr($tutor->apellidos, 0, 2) . substr($tutor->telefono, 0, 2);
            $model->password = Yii::$app->security->generateRandomString(10);
            // $model->contrasena = $model->password;
            $model->confirmar = $model->password;
            $model->nombre = $tutor->nombre;
            $model->apellidos = $tutor->apellidos;
            $model->direccion = $tutor->direccion;
            $model->nif = $tutor->nif;
            $model->tel_movil = $tutor->telefono;
            $model->email = $tutor->email;
            $model->colegio_id = $tutor->colegio_id;
            $model->rol = 'P';
            var_dump($model->validate());
            if ($model->save()) {
                $carr = new Carros();
                $carr->usuario_id = $model->id;
                if ($carr->save()) {
                    $model->emailRegistro();
                    Yii::$app->session->setFlash('info', 'Se le ha enviado un mensaje al padre para que se termine de registrar');
                    return $this->redirect(['tutores/index']);
                }
            }
        } else {
            $us = Yii::$app->user->identity;
            $model = new Usuarios();
            if ($us->rol === 'A') {
                if (Usuarios::findOne(['colegio_id' => $colegio_id, 'rol' => 'C']) != null) {
                    return $this->goHome();
                }
                $model->rol = 'C';
            } elseif ($us->rol === 'C') {
                if (Usuarios::findOne(['colegio_id' => $colegio_id, 'rol' => 'V']) != null) {
                    return $this->goHome();
                }
                $model->rol = 'V';
            } else {
                return $this->goHome();
            }
            $model->colegio_id = $colegio_id;

            if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }

            if ($model->load(Yii::$app->request->post())) {
                // $model->contrasena = $model->password;
                if ($model->save()) {
                    $model->emailRegistro();
                    Yii::$app->session->setFlash('info', 'Se le ha enviado un correo al usuario para que se registre');
                    return $this->redirect(['colegios/index']);
                }
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Usuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new Usuarios(['scenario' => Usuarios::ESCENARIO_CREATE]);
        $model->rol = 'P';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->email();
            return $this->goHome();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Usuarios::ESCENARIO_UPDATE;
        $model->password = '';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->nif === '') {
                $model->nif = null;
            }
            if ($model->email === '') {
                $model->email = null;
            }
            if ($model->save()) {
                return $this->goHome();
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
