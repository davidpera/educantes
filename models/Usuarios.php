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
    const ESCENARIO_CAMBIO = 'cambio';

    public $viejaPassword;
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
            'viejaPassword',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['email', 'rol'], 'required'],
            [
                 ['confirmar'],
                 'compare',
                 'compareAttribute' => 'password',
                 'skipOnEmpty' => false,
                 'on' => [self::ESCENARIO_CREATE, self::ESCENARIO_UPDATE],
             ],
             [
                 ['password', 'confirmar'],
                 'required',
                 'on' => [self::ESCENARIO_CAMBIO],
             ],
            [['tel_movil'], 'match', 'pattern' => '/^\d{9}$/'],
            [['colegio_id'], 'default', 'value' => null],
            [['colegio_id'], 'integer'],
            [['nom_usuario', 'password', 'nombre', 'apellidos', 'viejaPassword', 'direccion', 'email'], 'string', 'max' => 255],
            [['nif'], 'match', 'pattern' => '/^\d{8}[A-Z]{1}$/'],
            [['rol'], 'string', 'max' => 1],
            [['email'], 'email'],
            [['nif'], 'unique'],
            [['nom_usuario', 'email', 'tel_movil'], 'unique'],
            [['colegio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Colegios::className(), 'targetAttribute' => ['colegio_id' => 'id']],
        ];
        if ($this->rol === 'P') {
            $rules[] = [['password', 'confirmar', 'nom_usuario', 'nombre', 'apellidos', 'nif', 'direccion', 'tel_movil'], 'required', 'on' => self::ESCENARIO_CREATE];
        } else {
            $rules[] = [['password', 'confirmar', 'nom_usuario', 'tel_movil'], 'required', 'on' => self::ESCENARIO_CREATE];
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
            // 'contrasena' => 'Contraseña',
            'nombre' => 'Nombre',
            'apellidos' => 'Apellidos',
            'nif' => 'Nif',
            'direccion' => 'Dirección',
            'email' => 'Email',
            'tel_movil' => 'Telefono Movil',
            'rol' => 'Rol',
            'colegio_id' => 'Colegio',
            'confirmar' => 'Confirmar contraseña',
            'viejaPassword' => 'Antigua Contraseña',
        ];
    }

    public function smsStock($uniforme_id)
    {
        $uniform = Uniformes::findOne(['id' => $uniforme_id]);
        $secs = Secstocks::findOne(['uniforme_id' => $uniform->id]);
        if ($secs !== null && $uniform->cantidad <= $secs->mp) {
            $headers = ['Content-Type: application/json'];

            $ch = curl_init('http://api.gateway360.com/api/sms/submit_sms/?APIKEY=8e792f6f80ee4ddd7054f793a03142b6&SA=Educantes&DA=34' . $this->tel_movil . '&M=Deberia%20de%20pedir%20el%20uniforme%20codigo:%20' . $uniform->codigo . ',%20ya%20que%20su%20stock%20es%20igual%20o%20menor%20del%20stock%20de%20seguridad');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

            $result = curl_exec($ch);
            // var_dump('bien');
        }
    }

    /**
     * Email para CAmbiar la contraseña de un usuario si este se a olvidado de ella.
     * @param  string $token_val Token que nos dice que el usuario no sabe su contraseña
     */
    public function emailRecuperacion($token_val)
    {
        $mensaje = '<p>Para cambiar su contraseña pulse en el siguiente enlace</p>' . Html::a('Cambiar contraseña', Url::to(['usuarios/cambiar', 'token_val' => $token_val], true));
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Cambiar contraseña')
            ->setHtmlBody($mensaje)
            ->send();
    }

    /**
     * Email mandado cuando se da de alta a un usuario y que nos lleva a
     * la accion para completar el registro.
     */
    public function emailRegistro()
    {
        $mensaje = '<h3>Ha traves del siguiente enlace usted completara el registro y podra comprobar o rellenar sus datos personales</h3>' .
        Html::a('Acceder a la pagina web', Url::to(['usuarios/registro', 'id' => $this->id], true));
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Usted a sido registrado en la pagina web de Educantes')->setTextBody($mensaje)
            ->setHtmlBody($mensaje)
            ->send();
    }

    /**
     * Email que realiza el pedido de un padre a traves del carro.
     * @param  int $usuario Id del usuario que hace el pedido
     * @param  array $pedidos Todos los uniformes que componen el pedido
     */
    public function emailPedidoPadre($usuario, $pedidos)
    {
        if ($this->email !== null) {
            $email = $this->email;
        } else {
            $email = $this->colegio->email;
        }
        $total = 0.0;
        $totalIva = 0.0;
        $mensaje = '<table><tr><th>Codigo</th>' .
        '<th>Descripcion</th><th>Cantidad</th><th>Precio</th></tr>';
        foreach ($pedidos as $ped) {
            $num = preg_replace('/([^0-9\\,])/i', '', $ped[4]);
            $num = str_replace(',', '.', $num);
            $iv = preg_replace('/([^0-9\\,])/i', '', $ped[6]);
            $iv = str_replace(',', '.', $iv);
            $total += $num;
            $totalIva += $iv;
            $mensaje .= '<tr><td>' . $ped[1] . '</td>' .
            '<td>' . $ped[0] . '</td><td>' . $ped[3] . '</td>' .
            '<td>' . $ped[4] . '</td></tr>';
        }
        $pedido = json_encode($pedidos);
        // var_dump($pedido);
        // die();
        $mensaje .= '<tr><th colspan="3">Total</th><td>' . Yii::$app->formatter->asCurrency($total) . '</td></tr></table>' .
        '<tr><th colspan="3">Total con iva</th><td>' . Yii::$app->formatter->asCurrency($totalIva) . '</td></tr></table>' .
        Html::a('Confirmar', Url::to(['carros/aceptar', 'pedido' => $pedido, 'pedidorid' => $usuario], true));
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($email)
            ->setSubject('Se ha realizado un pedido a tu colegio')->setTextBody('A traves del enlace de este correo aceptaras el pedido y tendras que prepararlo')
            ->setHtmlBody($mensaje)
            ->send();
        // var_dump($mensaje);
        // die();
    }

    /**
     * Email que realiza el pedido simple.
     * @param  int $id             Id del uniforme pedido
     * @param  int $pedidorid      Id del usuario que realiza el pedido
     * @param  int $cantidadPedida Cantidad pedida del uniforme marcado
     */
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

    /**
     * Email que se realiza con el pedido multiple entre colegios.
     * @param  array $articulos Todos los uniformes que se piden a un cada colegio
     * @param  int $pedidorid Id de l usuario que ha realizado el pedido
     */
    public function emailMultiple($articulos, $pedidorid)
    {
        if ($this->email !== null) {
            $email = $this->email;
        } else {
            $email = $this->colegio->email;
        }
        $total = 0.0;
        $totalIva = 0.0;
        $mensaje = '<table><tr><th>Codigo</th>' .
        '<th>Descripcion</th><th>Cantidad</th><th>Precio</th></tr>';
        foreach ($articulos as $art) {
            $un = Uniformes::findOne(['id' => $art[0]]);
            $num = $art[1] * $un->precio;
            $iv = $num * ($un->iva / 100);
            $total += $num;
            $totalIva += $iv;
            $mensaje .= '<tr><td>' . $un->codigo . '</td>' .
            '<td>' . $un->descripcion . '</td><td>' . $art[1] . '</td>' .
            '<td>' . $art[1] * $un->precio . '</td></tr>';
        }
        $json = json_encode($articulos);
        // var_dump($pedido);
        // die();
        $mensaje .= '<tr><th colspan="3">Total</th><td>' . Yii::$app->formatter->asCurrency($total) . '</td></tr></table>' .
        '<tr><th colspan="3">Total con iva</th><td>' . Yii::$app->formatter->asCurrency($totalIva) . '</td></tr></table>' .
        Html::a('Aceptar', Url::to(['uniformes/aceptarmul', 'articulos' => $json, 'pedidorid' => $pedidorid, 'recibidor' => $this->id], true)) . ' ' .
        Html::a('Rechazar', Url::to(['uniformes/rechazarmul', 'articulos' => $json, 'pedidorid' => $pedidorid, 'recibidor' => $this->id], true));
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($email)
            ->setSubject('Se ha realizado un pedido a tu colegio')->setTextBody('A traves del enlace de este correo aceptaras el pedido y tendras que prepararlo')
            ->setHtmlBody($mensaje)
            ->send();
    }

    /**
     * Email que acepta el pedido del padre.
     * @param  array $pedido Todos los uniformes que han sido pedidos con sus cantidades
     */
    public function emailAceptarPadre($pedido)
    {
        $total = 0.0;
        $mensaje = '<table><tr><th>Codigo</th>' .
        '<th>Descripcion</th><th>Cantidad</th><th>Precio</th></tr>';
        foreach ($pedido as $ped) {
            $num = preg_replace('/([^0-9\\,])/i', '', $ped[4]);
            $num = str_replace(',', '.', $num);
            $total += $num;
            $mensaje .= '<tr><td>' . $ped[1] . '</td>' .
            '<td>' . $ped[0] . '</td><td>' . $ped[3] . '</td>' .
            '<td>' . $ped[4] . '</td></tr>';
        }
        $mensaje .= '<tr><th colspan="3">Total</th><td>' . Yii::$app->formatter->asCurrency($total) . '</td></tr></table>';
        $colegio = Colegios::find()->where(['id' => $this->colegio_id])->one();
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Han aceptado su pedido')->setTextBody('Han aceptado su pedido en el colegio ' . $colegio->nombre . ', ya puede ir a recogerlo')
            ->setHtmlBody('<h3>Han aceptado su siguente pedido en el colegio ' . $colegio->nombre . ', ya puede ir a recogerlo</h3>' . $mensaje)
            ->send();
    }

    /**
     * Email que acepta el pedido simple entre colegios.
     * @param  int $id Id del uniforme pedido
     */
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

    /**
     * Email que rechaza el pedido simple entre colegios.
     * @param  int $id Id del uniforme pedido
     */
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
     * Email que acepta el pedido multiple entre colegios.
     * @param  int $colegio_id Id del colegio al que se le ha hecho parte de ese pedido
     */
    public function emailAceptarmul($colegio_id)
    {
        $colegio = Colegios::find()->where(['id' => $colegio_id])->one();
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Han aceptado su pedido')->setTextBody('Han aceptado su parte del pedido multiple en el colegio ' . $colegio->nombre . ', ya puede ir a recogerlo')
            ->setHtmlBody('<h3>Han aceptado su parte del pedido multiple en el colegio ' . $colegio->nombre . ', ya puede ir a recogerlo</h3>')
            ->send();
    }

    /**
     * Email que rechaza el pedido multiple entre colegios.
     * @param  int $colegio_id Id del colegio al que se le ha hecho parte de ese pedido
     */
    public function emailRechazarmul($colegio_id)
    {
        $colegio = Colegios::find()->where(['id' => $colegio_id])->one();
        $resultado = Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['adminEmail'])
            ->setTo($this->email)
            ->setSubject('Han rechazado su pedido')->setTextBody('Lo siento, pero parte de su pedido multiple a sido rechazado en el colegio ' . $colegio->nombre)
            ->setHtmlBody('<h3>Lo siento, pero parte de su pedido multiple a sido rechazado en el colegio ' . $colegio->nombre . '</h3>')
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

    public function getCarro()
    {
        return $this->hasOne(Carros::className(), ['usuario_id' => 'id'])->inverseOf('usuario');
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
    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        // return $this->authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        // return $this->authKey === $authKey;
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

                if ($this->scenario === self::ESCENARIO_CREATE) {
                    // $this->contrasena = $this->password;
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
            } else {
                if ($this->scenario === self::ESCENARIO_CREATE) {
                    // $this->contrasena = $this->password;
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
                if ($this->scenario === self::ESCENARIO_UPDATE) {
                    if ($this->password === '') {
                        $this->password = $this->getOldAttribute('password');
                    } else {
                        $rol = Yii::$app->user->identity->rol;
                        if ($rol === 'A' || $rol === 'C') {
                            $this->password = Yii::$app->security->generatePasswordHash($this->password);
                        } else {
                            if (Yii::$app->getSecurity()->validatePassword($this->viejaPassword, $this->getOldAttribute('password'))) {
                                $this->password = Yii::$app->security->generatePasswordHash($this->password);
                            } else {
                                Yii::$app->session->setFlash('error', 'La contraseña antigua no coincide con la que ha puesto.');
                                return false;
                            }
                        }
                    }
                } elseif ($this->scenario === self::ESCENARIO_CAMBIO) {
                    $this->password = Yii::$app->security->generatePasswordHash($this->password);
                }
            }
            return true;
        }
        return false;
    }
}
