<?php

namespace app\traits;

use Yii;

trait JsTrait
{
    public function init() {
        parent::init();

        $path = $this->getViewPath();

        $jsFile = $path . '/scripts.js';

        Yii::$app->assetManager->publish($jsFile);
        $this->getView()->registerJsFile(
            Yii::$app->assetManager->getPublishedUrl($jsFile),
            ['depends' => 'app\assets\AppAsset']
        );
    }
}
