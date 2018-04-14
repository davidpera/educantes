<?php

namespace app\controllers;

use app\models\Alumnos;
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
                'only' => ['index', 'create', 'update', 'view', 'upload'],
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
     * @param  [type] $tabla [description]
     * @return [type]        [description]
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
                        } else {
                            $model = new Uniformes();
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
                            $no_permitidas = ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ', 'À', 'Ã', 'Ì', 'Ò', 'Ù', 'Ã™', 'Ã', 'Ã¨', 'Ã¬', 'Ã²', 'Ã¹', 'ç', 'Ç', 'Ã¢', 'ê', 'Ã®', 'Ã´', 'Ã»', 'Ã‚', 'ÃŠ', 'ÃŽ', 'Ã', 'Ã›', 'ü', 'Ã¶', 'Ã–', 'Ã¯', 'Ã¤', '«', 'Ò', 'Ã', 'Ã„', 'Ã‹', 'Ñ', 'à', 'è', 'ì', 'ò', 'ù'];
                            $permitidas = ['a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'n', 'N', 'A', 'E', 'I', 'O', 'U', 'a', 'e', 'i', 'o', 'u', 'c', 'C', 'a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U', 'u', 'o', 'O', 'i', 'a', 'e', 'U', 'I', 'A', 'E', 'N', 'a', 'e', 'i', 'o', 'u'];
                            $texto = str_replace($no_permitidas, $permitidas, $cade);

                            $vale = false;
                            for ($i = 0; $i < count($rows); $i++) {
                                if ($rows[$i]['column_name'] === $texto) {
                                    $vale = true;
                                }
                            }

                            if ($vale === false) {
                                Yii::$app->session->setFlash('error', 'Campos del archivo incorrectos, revise el archivo');
                                return $this->redirect(['upload', 'tabla' => $tabla]);
                            }

                            $model->$texto = $celda;
                        }
                        $model->save();
                    }
                    // return;
                    return $this->redirect(['alumnos/index']);
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
        if (Usuarios::find()->where(['id' => Yii::$app->user->id])->one()->rol !== 'A') {
            return $this->goHome();
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
     * Este método da de alta a un colegio.
     * @param  [type] $colegio_id [description]
     * @param null|mixed $id
     * @return [type]             [description]
     */
    public function actionAlta($colegio_id, $id = null)
    {
        if ($id !== null) {
            $tutor = Tutores::find()->where(['id' => $id])->one();
            $model = new Usuarios(['scenario' => Usuarios::ESCENARIO_CREATE]);
            $model->nom_usuario = substr($tutor->nombre, 0, 2) . substr($tutor->apellidos, 0, 2) . substr($tutor->telefono, 0, 2);
            $model->password = Yii::$app->security->generateRandomString(10);
            $model->confirmar = $model->password;
            $model->nombre = $tutor->nombre;
            $model->apellidos = $tutor->apellidos;
            $model->direccion = $tutor->direccion;
            $model->nif = $tutor->nif;
            $model->tel_movil = $tutor->telefono;
            $model->email = $tutor->email;
            $model->colegio_id = $tutor->colegio_id;
            $model->rol = 'P';
            if ($model->save()) {
                return $this->redirect(['tutores/index']);
            }
        }

        $us = Yii::$app->user->identity;
        $model = new Usuarios(['scenario' => Usuarios::ESCENARIO_CREATE]);
        if ($us->rol === 'A') {
            $model->rol = 'C';
        } elseif ($us->rol === 'C') {
            $model->rol = 'V';
        } else {
            return $this->goHome();
        }
        $model->colegio_id = $colegio_id;


        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['colegios/index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
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
        $model = Yii::$app->user->identity;
        $model->scenario = Usuarios::ESCENARIO_UPDATE;
        $model->password = '';

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goHome();
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
