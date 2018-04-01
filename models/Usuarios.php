<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nom_usuario
 * @property string $password
 * @property string $nombre
 * @property string $apellidos
 * @property string $nif
 * @property string $direccion
 * @property string $email
 * @property string $tel_movil
 * @property string $rol
 * @property int $colegio_id
 *
 * @property Correos[] $correos
 * @property Facturas[] $facturas
 * @property Sms[] $sms
 * @property Colegios $colegio
 */
class Usuarios extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    const ESCENARIO_CREATE = 'create';
    const ESCENARIO_UPDATE = 'update';

    public $confirmar;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    public function attributes()
    {
        return array_merge(parent::attributes(), [
            'confirmar',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['nom_usuario', 'rol'], 'required'],
            [['password', 'confirmar'], 'required', 'on' => self::ESCENARIO_CREATE],
            [
                 ['confirmar'],
                 'compare',
                 'compareAttribute' => 'password',
                 'skipOnEmpty' => false,
                 'on' => [self::ESCENARIO_CREATE, self::ESCENARIO_UPDATE],
             ],
            [['tel_movil'], 'number', 'min' => 100000000, 'max' => 999999999],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'direccion', 'email'], 'string', 'max' => 255],
            [['nif'], 'string', 'max' => 9],
            [['rol'], 'string', 'max' => 1],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['nif'], 'unique'],
            [['nom_usuario'], 'unique'],
            [['tel_movil'], 'unique'],
            [['colegio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Colegios::className(), 'targetAttribute' => ['colegio_id' => 'id']],
        ];
        if (Yii::$app->user->isGuest || Yii::$app->user->identity->rol === 'P') {
            $rules[] = [['nombre', 'apellidos', 'direccion', 'nif', 'email', 'tel_movil'], 'required'];
        }
        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nom_usuario' => 'Nombre de Usuario',
            'password' => 'Contraseña',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'nif' => 'Nif',
            'direccion' => 'Dirección',
            'email' => 'Email',
            'tel_movil' => 'Telefono Movil',
            'rol' => 'Rol',
            'colegio_id' => 'Colegio ID',
            'confirmar' => 'Confirmar contraseña',
        ];
    }

    public function email()
    {
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Validación de tu cuenta de email')->setTextBody('A traves del enlace de este correo verificaras tu cuenta de email')
            ->setHtmlBody(Html::a('verificar', Url::to(['usuarios/verificar', 'token_val' => $this->token_val], true)))
            ->send();
        Yii::$app->session->setFlash('info', 'Se le ha enviado un correo');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorreos()
    {
        return $this->hasMany(Correos::className(), ['receptor_id' => 'id'])->inverseOf('receptor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFacturas()
    {
        return $this->hasMany(Facturas::className(), ['usuario_id' => 'id'])->inverseOf('usuario');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms()
    {
        return $this->hasMany(Sms::className(), ['receptor_id' => 'id'])->inverseOf('receptor');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColegio()
    {
        return $this->hasOne(Colegios::className(), ['id' => 'colegio_id'])->inverseOf('usuarios');
    }
    public static function findIdentity($id)
    {
        return self::findOne($id);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }
    public function getId()
    {
        return $this->id;
    }
    public function getAuthKey()
    {
        // return $this->auth_key;
    }
    public function validateAuthKey($authKey)
    {
        // return $this->getAuthKey() === $authKey;
    }
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                // $this->auth_key = Yii::$app->security->generateRandomString();
                if (Yii::$app->user->isGuest) {
                    $this->token_val = Yii::$app->security->generateRandomString();
                }
                if ($this->scenario === self::ESCENARIO_CREATE) {
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
            } else {
                if ($this->scenario === self::ESCENARIO_UPDATE) {
                    if ($this->password === '') {
                        $this->password = $this->getOldAttribute('password');
                    } else {
                        $this->password = Yii::$app->security->generatePasswordHash($this->password);
                    }
                }
            }
            return true;
        }
        return false;
    }
}
