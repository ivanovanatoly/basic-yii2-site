<?php
namespace app\assets;

use yii\web\AssetBundle;

class ColorPickerAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        'plugins/huebee/huebee.min.css',
    ];

    public $js = [
        'plugins/huebee/huebee.pkgd.min.js'
    ];

    public $depends = [
        'app\assets\AppAsset'
    ];
}