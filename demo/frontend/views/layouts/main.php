<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

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
//        'brandLabel' => Yii::$app->name,
//        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '首页', 'url' => ['/index/index']],
        ['label' => '菜谱', 'url' => ['/menu/index']],
        ['label' => '分享文章', 'url' => ['/article/index']],
        ['label' => '社区', 'url' => ['/community/index']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '注册', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
//        'style' =>['background-color'=>'#e8e8e8'],
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
        <div class="cght">
            ©2018-2019 <a href="http://www.douguo.com" target="_blank">龙岩学院2015级软工</a>
            <a href="http://www.miibeian.gov.cn" target="_blank" rel="nofollow">京ICP证111032号</a>
            <a href="http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=11010102001133" target="_blank">
                京公网安备11010102001133号
            </a>
            <a href="http://sq.ccm.gov.cn/ccnt/sczr/doLogin" target="_blank">
                <img src="https://i1.douguo.com//upload/banner/1522057799.png" style="margin-bottom: -3px;width:20px">
                京网文【2017】6954-770号 食品流通许可证SP1101061510252413
            </a>

        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
