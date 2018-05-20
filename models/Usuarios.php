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
            [['password', 'contrasena', 'confirmar'], 'required', 'on' => self::ESCENARIO_CREATE],
            [
                 ['confirmar'],
                 'compare',
                 'compareAttribute' => 'password',
                 'skipOnEmpty' => false,
                 'on' => [self::ESCENARIO_CREATE, self::ESCENARIO_UPDATE],
             ],
            [['tel_movil'], 'match', 'pattern' => '/^\d{9}$/'],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'direccion', 'email'], 'string', 'max' => 255],
            [['nif'], 'match', 'pattern' => '/^\d{8}[A-Z]{1}$/'],
            [['rol'], 'string', 'max' => 1],
            [['email'], 'email'],
            [['nif'], 'unique'],
            [['nom_usuario', 'email', 'tel_movil'], 'unique'],
            [['colegio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Colegios::className(), 'targetAttribute' => ['colegio_id' => 'id']],
        ];
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
            'contrasena' => 'Contraseña',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'nif' => 'Nif',
            'direccion' => 'Dirección',
            'email' => 'Email',
            'tel_movil' => 'Telefono Movil',
            'rol' => 'Rol',
            'colegio_id' => 'Colegio',
            'confirmar' => 'Confirmar contraseña',
        ];
    }

    public function emailPedido($id, $pedidorid, $cantidadPedida)
    {
        if ($this->email !== null) {
            $email = $this->email;
        } else {
            $email = $this->colegio->email;
        }
        $uniforme = Uniformes::find()->where(['id' => $id])->one();
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($email)
            ->setSubject('Se ha realizado un pedido a tu colegio')->setTextBody('A traves del enlace de este correo aceptaras el pedido y tendras que prepararlo')
            ->setHtmlBody('<h3>Ha recibido un pedido de ' . $cantidadPedida . ' ' . $uniforme->descripcion . '<br/>' .
            Html::a('Aceptar', Url::to(['uniformes/aceptar', 'id' => $id, 'pedidorid' => $pedidorid], true)) . ' ' .
            Html::a('Rechazar', Url::to(['uniformes/rechazar', 'id' => $id, 'pedidorid' => $pedidorid, 'cantidadPedida' => $cantidadPedida], true)))
            ->send();
    }

    public function emailMultiple($articulos, $pedidorid)
    {
        if ($this->email !== null) {
            $email = $this->email;
        } else {
            $email = $this->colegio->email;
        }
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($email)
            ->setSubject('Se ha realizado un pedido a tu colegio')->setTextBody('A traves del enlace de este correo aceptaras el pedido y tendras que prepararlo')
            ->setHtmlBody('<h3>Ha recibido un pedido de varios articulos<br/>' .
            Html::a('Aceptar', Url::to(['uniformes/aceptarmul', 'articulos' => $articulos, 'pedidorid' => $pedidorid], true)) . ' ' .
            Html::a('Rechazar', Url::to(['uniformes/rechazarmul', 'articulos' => $articulos, 'pedidorid' => $pedidorid], true)))
            ->send();
    }

    public function emailAceptar($id)
    {
        $uniforme = Uniformes::find()->where(['id' => $id])->one();
        $colegio = Colegios::find()->where(['id' => $this->colegio_id])->one();
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Han aceptado su pedido')->setTextBody('Han aceptado su pedido de ' . $uniforme->descripcion . ' en el colegio ' . $colegio->nombre . ', ya puede ir a recogerlo')
            ->setHtmlBody('<h3>Han aceptado su pedido de ' . $uniforme->descripcion . ' en el colegio ' . $colegio->nombre . ', ya puede ir a recogerlo</h3>')
            ->send();
    }

    public function emailRechazar($id)
    {
        $uniforme = Uniformes::find()->where(['id' => $id])->one();
        $colegio = Colegios::find()->where(['id' => $this->colegio_id])->one();
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Han rechazado su pedido')->setTextBody('Lo siento, pero su pedido de ' . $uniforme->descripcion . ' al colegio ' . $colegio->nombre . ' ha sido rechazado')
            ->setHtmlBody('<h3>Lo siento, pero su pedido de ' . $uniforme->descripcion . ' al colegio ' . $colegio->nombre . ' ha sido rechazado</h3>')
            ->send();
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
                    $query = Alumnos::find()->where(['colegio_id' => Yii::$app->request->post()['Usuarios']['colegio_id']])
                    ->andWhere(['nombre' => Yii::$app->request->post()['Hijo']['nombre']])
                    ->andWhere(['primer_apellido' => Yii::$app->request->post()['Hijo']['prim-ape']])
                    ->andWhere(['fecha_de_nacimiento' => Yii::$app->request->post()['Hijo']['fech-nac']]);
                    if (Yii::$app->request->post()['Hijo']['sec-ape'] !== '') {
                        $query->andWhere(['segundo_apellido' => Yii::$app->request->post()['Hijo']['sec-ape']]);
                    }
                    $hijo = $query->one();
                    if ($hijo === null) {
                        Yii::$app->session->setFlash('error', 'El alumno escogido no existe el el colegio indicado, compruebe que ha elegido bien el colegio');
                        return false;
                    }
                    $this->token_val = Yii::$app->security->generateRandomString();
                }
                if ($this->scenario === self::ESCENARIO_CREATE) {
                    $this->contrasena = $this->password;
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
