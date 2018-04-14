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
    NavBar::begin([
        'brandLabel' => Html::img('/uploads/icono.png', [
            'alt' => 'Educantes',
            'width' => '30px;',
            'style' => 'display: inline; margin-top: -3px;',
        ]) . ' ' . Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $items = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'About', 'url' => ['/site/about']],
        ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (!Yii::$app->user->isGuest) {
        $us = Usuarios::find()->where(['id'=>Yii::$app->user->id])->one();
        if ($us->rol !== 'P') {
            if ($us->rol !== 'V') {
                $items[] = ['label' => 'Lista Usuarios', 'url' => ['/usuarios/index']];
                if ($us->rol === 'A') {
                    $items2 =[
                        ['label' => 'Gestionar colegios', 'url' => ['/colegios/gestionar']]
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
                    $items[] = [
                        'label' => 'Libros',
                        'items' => [
                            ['label' => 'Insertar Libros', 'url' => ['usuarios/upload', 'tabla' => 'libros']],
                            '<li class="divider"></li>',
                            ['label' => 'Ver libros', 'url' => ['libros/index']],
                        ],
                    ];
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
                }
            }
            $items2[] = ['label' => 'Datos colegios', 'url' => ['/colegios/index']];
            $items[] = [
                'label' => 'Colegios',
                'items' => $items2,
            ];
        }
    }
    Yii::$app->user->isGuest ? (
        $items[] = [
            'label' => 'Usuarios',
            'items' => [
                ['label' => 'Login', 'url' => ['/site/login']],
                ['label' => 'Registrarse', 'url' => ['usuarios/create']],
            ],
        ]
    ) : (
        $items[] = [
            'label' => 'Usuarios ('.Yii::$app->user->identity->nom_usuario.')',
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
