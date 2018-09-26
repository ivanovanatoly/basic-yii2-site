<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Nav;
use yii\helpers\Html;

/**
 * @var yii\web\View 				    $this
 * @var app\modules\user\models\User 	$user
 */

$this->title = Yii::t('user', 'Create a user account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php echo Nav::widget([
                    'options' => [
                        'class' => 'nav-pills nav-stacked',
                    ],
                    'items' => [
                        ['label' => Yii::t('user', 'Account details'), 'url' => ['/user/admin/create']],
                        ['label' => Yii::t('user', 'Profile details'), 'options' => [
                            'class' => 'disabled',
                            'onclick' => 'return false;',
                        ]],
                        ['label' => Yii::t('user', 'Information'), 'options' => [
                            'class' => 'disabled',
                            'onclick' => 'return false;',
                        ]],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="alert alert-info">
                    <?php echo Yii::t('user', 'Credentials will be sent to the user by email') ?>.
                    <?php echo Yii::t('user', 'A password will be generated automatically if not provided') ?>.
                </div>
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'enableAjaxValidation'   => true,
                    'enableClientValidation' => false,
                    'fieldConfig' => [
                        'horizontalCssClasses' => [
                            'wrapper' => 'col-sm-9',
                        ],
                    ],
                ]); ?>

                <?php echo $this->render('_user', ['form' => $form, 'user' => $user]) ?>

                <div class="form-group">
                    <div class="col-lg-offset-3 col-lg-9">
                        <?php echo Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-block btn-success']) ?>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
