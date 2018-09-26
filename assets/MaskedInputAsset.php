<?php

namespace app\assets;

use yii\web\AssetBundle;

class MaskedInputAsset extends AssetBundle
{
    public $js = [
        'plugins/masked-input/jquery.maskedinput.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
