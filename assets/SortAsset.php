<?php
namespace app\assets;

use yii\web\AssetBundle;

class SortAsset extends AssetBundle
{
    public $js = [
        'plugins/jquery.tablesorter/jquery.tablesorter.min.js',
        'js/common/sort.js'
    ];
    public $depends = [
        'app\assets\SportspringAsset',
    ];
}