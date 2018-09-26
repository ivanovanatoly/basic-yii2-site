<?php
namespace app\assets;

use yii\web\AssetBundle;

class SlickAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'plugins/slick/slick.css'
    ];
    public $js = [
        'plugins/slick/slick.min.js',
    ];
    public $depends = [
        'app\assets\SportspringAsset'
    ];
}