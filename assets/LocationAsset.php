<?php
namespace app\assets;

use yii\web\AssetBundle;

class LocationAsset extends AssetBundle
{
    public $js = [
        'http://api-maps.yandex.ru/2.1-dev/?lang=ru-RU&load=package.full',
        'js/common/location.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
}