<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'app\commands',
    'modules' => [],
    'components' => [
        'storage' => [
            'class'        => 'app\components\storage\S3Storage',
            'region'       => 'eu-central-1',
            'key'          => 'AKIAIHTJ3LO6V3CPWOWQ',
            'secret'       => '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',
            'bucket'       => 'dev.st.sportspring.ru',
        ],
        'redis' => [
            'class'     => 'yii\redis\Connection',
            'hostname'  => 'localhost',
            'port'      => 6379,
            'database'  => 1
        ],
        'cache' => [
            'class'     => 'yii\redis\Cache',
            'redis'     => 'redis',
            'keyPrefix' => 'cache'
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=basic',
            'username' => 'root',
            'password' => '1234567890',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'menu' => 'menu.php'
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*']
    ];
}

return $config;
