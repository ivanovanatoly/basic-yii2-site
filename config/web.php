<?php

$params = require(__DIR__ . '/params.php');

if (file_exists(__DIR__ . '/params.local.php')) {
    $params = array_merge(
        $params,
        require __DIR__ . '/params.local.php'
    );
}

$config = [
    'id' => 'basic',
    'name' => 'basic.local',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'bootstrap' => [
        'log',
        'app\modules\user\Bootstrap',
    ],
    'container' => [
        'definitions' => [
            'app\widgets\Chatra\Chatra'           => ['id' => ''],
            'app\widgets\SocialShare\SocialShare' => ['id' => '']
        ]
    ],
    'modules' => [
        'user' => [
            'class'     => 'app\modules\user\Module',
            'admins'    => ['admin'],
            'layout'    => '/nifty/cabinet',
        ],
    ],
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
        'session' => [
            'class'     => 'yii\redis\Session',
            'redis'     => 'redis',
            'keyPrefix' => 'session'
        ],
        'cache' => [
            'class'     => 'yii\redis\Cache',
            'redis'     => 'redis',
            'keyPrefix' => 'cache'
        ],
        'kue' => [
            'class'     => 'app\components\Kue',
            'redis'     => 'redis'
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'showScriptName'      => false,
            'enableStrictParsing' => false,
            'rules' => require(__DIR__ . '/rules.php'),
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
        'request' => [
            'cookieValidationKey' => 'YCK0ibf-VNjcsaN6TZrGH_o_aFas5pl4',
        ],
        'errorHandler' => [
            'errorAction' => 'frontend/site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
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
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=basic',
            'username' => 'root',
            'password' => '1234567890',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'imageMaker' => [
            'class'      => 'app\components\imagemaker\ImageMaker',
            'serviceUrl' => 'http://95.213.203.213:3000'
        ],
        'vkApi' => [
            'class'    => 'app\components\VkApi',
            'clientId' => '3912868',
            'ownerId'  => '226100141',
            //https://oauth.vk.com/authorize?client_id=3912868&redirect_uri=http://api.vk.com/blank.html&scope=groups,wall,photos,offline&display=wap&response_type=token
            'token'    => '3ef751fe535a9422decef8d128bde2304a6db28dbb71cb4ad9ad6cb8181f0056c172f21932ac76a4ee3ce'
        ]
    ],
    'on beforeAction' => function($event){
        if (extension_loaded('newrelic')) {
            $app = Yii::$app;
            newrelic_name_transaction(
                ($app->controller->module ? $app->controller->module->id . '/' : '')
                . $app->controller->id
                . '/'
                . $app->requestedAction->id
            );
        }
    },
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*']
    ];
}

return $config;
