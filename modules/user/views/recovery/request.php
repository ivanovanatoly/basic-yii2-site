<?php

use yii\bootstrap\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\user\models\RecoveryForm $model
 */

$this->title = Yii::t('user', 'Recover your password');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="cls-content">
    <div class="cls-content-sm panel">
        <div class="panel-body">
            <div class="pad-ver">
                <i class="pli-mail icon-3x"></i>
            </div>
            <p class="text-muted pad-btm">Введите E-mail, который вы указали при регистрации, мы вышлем на него инструкции по восстановлению пароля: </p>
            <?php $form = ActiveForm::begin([
                'id'                     => 'password-recovery-form',
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
            ]); ?>

            <?php echo $form->field($model, 'email', [
                'inputOptions' => [
                    'placeholder' => 'Email',
                    'autofocus'   => true,
                ],
                'template' => '{input}',
            ])->label(false); ?>
            <div class="form-group text-right">
                <button class="btn btn-success btn-block" type="submit">Восстановить пароль</button>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="pad-top">
                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['/user/security/login']); ?>" class="btn-link mar-rgt">Назад</a>
            </div>
        </div>
    </div>
</div>
