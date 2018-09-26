<?php

namespace app\assets;

use yii\web\AssetBundle;

class BasicAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'plugins/font-awesome/css/font-awesome.min.css',
        'css/normalize.css',
    ];

    public $js = [
        'plugins/jquery.nicescroll/jquery.nicescroll.min.js',
        'plugins/jquery.cookie/jquery.cookie.js',
        'js/scripts.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}
