<?php

namespace app\controllers;

use app\models\Alumnos;
use app\models\Libros;
use app\models\Uniformes;
use app\models\UploadForm;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

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
                'only' => ['index', 'update', 'view', 'upload'],
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
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
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

    public function actionUpload($tabla)
    {
        if (Yii::$app->user->identity->rol === 'C') {
            $model = new UploadForm();

            if (Yii::$app->request->isPost) {
                $model->file_alum = UploadedFile::getInstance($model, 'file_alum');
                if ($model->upload()) {
                    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load('uploads/test.xlsx');

                    $worksheet = $spreadsheet->getActiveSheet();
                    // Get the highest row number and column letter referenced in the worksheet
                    $highestRow = $worksheet->getHighestRow(); // e.g. 10
                    $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                    // Increment the highest column letter
                    $highestColumn++;

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
                            $model->$campo = $celda;
                        }
                        $model->save();
                    }
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

    public function actionAlta($colegio_id)
    {
        $us = Usuarios::find()->where(['id' => Yii::$app->user->id])->one();
        $model = new Usuarios();
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
        $model = new Usuarios();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
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

        return $this->redirect(['colegios/index']);
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
