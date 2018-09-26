<?php

$config = [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=sportspring',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ],
        'redis' => [
            'class'     => 'yii\redis\Connection',
            'hostname'  => 'localhost',
            'port'      => 6379,
            'database'  => 1
        ],
    ],
];

return $config;
