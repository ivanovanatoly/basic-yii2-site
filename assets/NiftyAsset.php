<?php

namespace app\assets;

use yii\web\AssetBundle;

class NiftyAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/nifty.min.css',
        'http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&amp;subset=latin',
        //TODO выпилить из проекта themify-icons
        'css/themify-icons/themify-icons.min.css',
        'plugins/font-awesome/css/font-awesome.min.css',
        'plugins/magic-check/css/magic-check.min.css'
    ];
    public $js = [
        'js/nifty.min.js',
        'plugins/bootbox/bootbox.min.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
