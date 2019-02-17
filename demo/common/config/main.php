<?php
require_once(__DIR__.'/situation.php');
require_once(__DIR__.'/../CommonFunction.php');
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'bootstrap' => ['log','queue'],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'queue' => [
            'class' => \yii\queue\redis\Queue::class,
            'redis' => 'redis', // Redis connection component or its config
            'channel' => 'queue', // Queue channel key
        ],
    ],
];
