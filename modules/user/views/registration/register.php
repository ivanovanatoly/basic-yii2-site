<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use \app\modules\user\widgets\Connect;

/**
 * @var yii\web\View                 $this
 * @var app\modules\user\models\User $user
 * @var app\modules\user\Module      $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php echo $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="cls-content">
    <div class="cls-content-lg panel">
        <div class="panel-body">
            <div class="mar-ver pad-btm">
                <h3 class="h4 mar-no">Создать аккаунт</h3>
                <p class="text-muted">Присоединяйтесь к СЛ СПБ.</p>
            </div>
            <?php $form = ActiveForm::begin([
                'id'                     => 'registration-form',
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
            ]); ?>
            <div class="row">
                <div class="col-sm-6">

                    <?php echo $form->field($model, 'first_name', [
                        'inputOptions' => ['placeholder' => 'Имя'],
                        'template'     => '{input}',
                    ])->label(false); ?>

                    <?php echo $form->field($model, 'last_name', [
                        'inputOptions' => ['placeholder' => 'Фамилия'],
                        'template'     => '{input}',
                    ])->label(false); ?>

                </div>
                <div class="col-sm-6">

                    <?php echo $form->field($model, 'email', [
                        'inputOptions' => ['placeholder' => 'Email'],
                        'template'     => '{input}',
                    ])->label(false); ?>

                    <?php if ($module->enableGeneratingPassword == false) { ?>
                        <?php echo $form->field($model, 'password', [
                            'inputOptions' => ['placeholder' => 'Пароль'],
                            'template'     => '{input}',
                        ])->label(false)->passwordInput(); ?>
                    <?php } ?>

                </div>
            </div>
            <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-primary btn-block']); ?>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="pad-all">
            Уже есть аккаунт ? <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['/user/security/login']); ?>" class="btn-link mar-rgt">Вход</a>

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
                <div class="media-body text-left text-muted">
                    Вход через соцсети
                </div>
            </div>
        </div>
    </div>
</div>
