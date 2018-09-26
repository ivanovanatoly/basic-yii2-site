<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \app\modules\user\widgets\Connect;

/**
 * @var yii\web\View                      $this
 * @var app\modules\user\models\LoginForm $model
 * @var app\modules\user\Module           $module
 */

$this->title = Yii::t('*', 'Вход в личный кабинет');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="cls-content">
    <div class="cls-content-sm panel">
        <div class="panel-body">
            <div class="mar-ver pad-btm">
                <h3 class="h4 mar-no"><?php echo $this->title; ?></h3>

                <p class="text-muted">Авторизуйтесь под своим аккаунтом</p>
            </div>

            <?php $form = ActiveForm::begin([
                'id'                     => 'login-form',
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
                'validateOnBlur'         => false,
                'validateOnType'         => false,
                'validateOnChange'       => false,
            ]) ?>

            <?= $form->field($model, 'login', [
                'inputOptions' => [
                    'placeholder' => 'Email',
                    'autofocus' => 'autofocus',
                    'tabindex' => '1'
                ],
                'template' => '{input}',
            ])->label(false); ?>

            <?php echo $form->field($model, 'password', [
                'inputOptions' => [
                    'placeholder' => 'Пароль',
                    'tabindex'    => '2'
                ]
            ])->label(false)->passwordInput(); ?>

            <?php echo $form->field($model, 'rememberMe', [
                'options'  => ['tag' => false],
                'template' => "<div class=\"checkbox pad-btm text-left\">{input} {label}</div>",
            ])->checkbox(['class' => 'magic-checkbox', 'tabindex' => '4'], false)->label('Запомнить') ?>

            <?php echo Html::submitButton(Yii::t('*', 'Войти в личный кабинет'), ['class' => 'btn btn-primary btn-lg btn-block', 'tabindex' => '3']) ?>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="pad-all">
            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['/user/recovery/request']); ?>" class="btn-link mar-rgt">Забыли пароль ?</a>
            <?php if ($module->enableRegistration): ?>
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['/user/registration/register']); ?>" class="btn-link mar-lft">Создать новый аккаунт</a>
            <?php endif ?>

            <div class="media pad-top bord-top">
                <div class="pull-right">
                    <div class="auth-clients">
                        <?php $authAuthChoice = Connect::begin([
                            'baseAuthUrl' => ['/user/security/auth'],
                        ]); ?>
                        <?php foreach ($authAuthChoice->getClients() as $client): ?>
                            <?php echo $authAuthChoice->clientLink($client, sprintf('<i class="fa fa-%s icon-lg text-primary"></i>', $client->getId()), ['class' => 'pad-rgt']) ?>
                        <?php endforeach; ?>
                        <?php Connect::end(); ?>
                    </div>
                </div>
                <div class="media-body text-left">
                    Вход через соцсети
                </div>
            </div>
        </div>
    </div>
</div>
