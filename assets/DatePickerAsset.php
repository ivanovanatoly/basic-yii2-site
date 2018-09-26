<?php
namespace app\assets;

use yii\web\AssetBundle;

class DatePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';

    public $baseUrl = '@web';

    public $css = [
        '/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css',
    ];

    public $depends = [
        'dosamigos\datepicker\DatePickerAsset',
        'app\assets\AppAsset'
    ];
}