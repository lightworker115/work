<?php
require_once(__DIR__.'/../models/helper.php');
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'defaultRoute'=>'index/index',
    'modules' => [
        'card'=>[
            'class'=> 'api\modules\card\index',
            'defaultRoute'=>'index/index'
        ],
        'staff'=>[
            'class'=> 'api\modules\staff\index',
            'defaultRoute'=>'index/index'
        ],
        'boss'=>[
            'class'=> 'api\modules\boss\index',
            'defaultRoute'=>'index/index'
        ],
        'shop'=>[
            'class'=> 'api\modules\shop\index',
            'defaultRoute'=>'index/index'
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
//        'errorHandler' => [
//            'errorAction' => 'site/error',
//        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                'view/<id:\d+>' => 'site/view',
                //设置自己的路由规则，这里我设置了一个控制器里面的一个方法的规则,只要满足了这个规则就会跳转到相应的方法去处理
                "<controller:\w+>/<action:\w+>"=>"<controller>/<action>",
                "user/reg/<id:\d+>"=>"user/reg",
                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>'
            ]
        ],
    ],
    'params' => $params,
];
