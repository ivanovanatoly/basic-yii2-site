<?php

namespace app\controllers\frontend;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'frontend/default';
        return $this->render('index');
    }

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception !== null) {

        }
    }
}
