<?php

namespace app\assets;

use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
    public $css = [
        'plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css',
    ];
    public $depends = [
        'dosamigos\datetimepicker\DateTimePickerAsset',
    ];
}
