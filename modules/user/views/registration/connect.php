<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\user\models\User $model
 * @var app\modules\user\models\Account $account
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="cls-content">
    <div class="cls-content-sm panel">
        <div class="panel-alert">
            <div class="alert-wrap in">
                <div class="alert alert-primary">
                    <div class="media">
                        <?php echo Yii::t('user', 'In order to finish your registration, we need you to enter your email address') ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'id' => 'connect-account-form',
            ]); ?>

            <?php echo $form->field($model, 'email') ?>

            <?php echo $form->field($model, 'username') ?>

            <?php echo Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-success btn-block']) ?>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="pad-all bord-top">
            <?php echo Html::a(Yii::t('user', 'If you already registered, sign in and connect this account on settings page'), ['/user/settings/networks']) ?>.
        </div>
    </div>
</div>
