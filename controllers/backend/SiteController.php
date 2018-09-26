<?php

namespace app\controllers\backend;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->redirect(['cabinet']);
    }

    public function actionCabinet()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/user/security/login']);
        }

        $this->layout = 'nifty/cabinet';

        return $this->render('index');
    }
}
