<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\models\Usuarios;

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">

    <?php
    $options = ['class' => 'navbar-inverse navbar-fixed-top'];
    $nombre = Yii::$app->name;
    if (!Yii::$app->user->isGuest) {
        $nombre = $nombre . ' ('.Yii::$app->user->identity->colegio->nombre.')';
        if (Yii::$app->user->identity->rol === 'C') {
            $options = ['class' => 'navbar-inverse navbar-fixed-top adcol'];
        }
    }

    NavBar::begin([
        'brandLabel' => Html::img('/uploads/icono.png', [
            'alt' => 'Educantes',
            'width' => '30;',
            'style' => 'display: inline; margin-top: -3;',
        ]) . ' ' . $nombre,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => $options,
    ]);
    // $items = [
    //     ['label' => 'Inicio', 'url' => ['/site/index']],
    // ];
    if (!Yii::$app->user->isGuest) {
        $us = Usuarios::find()->where(['id'=>Yii::$app->user->id])->one();
        if ($us->rol !== 'P') {
            if ($us->rol !== 'V') {
                $items[] = ['label' => 'Usuarios', 'url' => ['/usuarios/index']];
                if ($us->rol === 'A') {
                    $items2 =[
                        ['label' => 'Gestionar colegios', 'url' => ['/colegios/gestionar']],
                        ['label' => 'Datos colegios', 'url' => ['/colegios/index']]
                    ];
                    $items[] = [
                        'label' => 'Colegios',
                        'items' => $items2,
                    ];
                } else {
                    $items[] = [
                        'label' => 'Uniformes',
                        'items' => [
                            ['label' => 'Insertar Uniformes', 'url' => ['usuarios/upload', 'tabla' => 'uniformes']],
                            '<li class="divider"></li>',
                            ['label' => 'Ver uniformes', 'url' => ['uniformes/index']],
                        ],
                    ];
                    // $items[] = [
                    //     'label' => 'Libros',
                    //     'items' => [
                    //         ['label' => 'Insertar Libros', 'url' => ['usuarios/upload', 'tabla' => 'libros']],
                    //         '<li class="divider"></li>',
                    //         ['label' => 'Ver libros', 'url' => ['libros/index']],
                    //     ],
                    // ];
                    $items[] = [
                        'label' => 'Alumnos',
                        'items' => [
                            ['label' => 'Insertar Alumnos', 'url' => ['usuarios/upload', 'tabla' => 'alumnos']],
                            '<li class="divider"></li>',
                            ['label' => 'Ver alumnos', 'url' => ['alumnos/index']],
                        ],
                    ];
                    $items[] = [
                        'label' => 'Tutores',
                        'items' => [
                            ['label' => 'Insertar Tutores', 'url' => ['usuarios/upload', 'tabla' => 'tutores']],
                            '<li class="divider"></li>',
                            ['label' => 'Ver Tutores', 'url' => ['tutores/index']],
                        ],
                    ];
                    $items[] = ['label' => 'Colegios', 'url' => ['/colegios/index']];

                }
            } else {
                $items[] = ['label' => 'Uniformes','url' => ['/uniformes/index']];
            }
        } else {
            $items[] = ['label' => 'Pedidos', 'url' => ['/carros/realizados']];
            $items[] = ['label' => ' ('.Yii::$app->user->identity->carro->productos.')', 'url' => ['/carros/carrito'], 'linkOptions' => ['class' => 'glyphicon glyphicon-shopping-cart']];
            $items[] = ['label' => 'Productos', 'url' => ['/uniformes/index']];
        }
    }
    Yii::$app->user->isGuest ? (
        $items[] = ['label' => 'Login', 'url' => ['/site/login']]
    ) : (
        $items[] = [
            'label' => '('.Yii::$app->user->identity->nom_usuario.')',
            'items' => [
                ['label' => 'Modificar datos', 'url' => ['usuarios/update', 'id' => Yii::$app->user->id]],
                '<li class="divider"></li>',
                [
                    'label' => 'Logout',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'POST'],
                ],
            ],
        ]
    );

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $items,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; Educantes <?= date('Y') ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
