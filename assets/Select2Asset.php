<?php

namespace app\assets;

use yii\web\AssetBundle;

class Select2Asset extends AssetBundle
{
    public $css = [
        'plugins/select2/css/select2.min.css',
    ];
    public $js = [
        'plugins/select2/js/select2.min.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
