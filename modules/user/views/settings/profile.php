<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var app\modules\user\models\Profile $profile
 */

$this->title = Yii::t('user', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render('_menu') ?>
    </div>
    <div class="col-md-9">
        <div class="panel">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
            </div>
            <?php $form = \yii\widgets\ActiveForm::begin([
                'id' => 'profile-form',
                'options' => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template' => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                    'labelOptions' => ['class' => 'col-lg-3 control-label'],
                ],
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
                'validateOnBlur'         => false,
            ]); ?>
            <div class="panel-body">

                <?= $form->field($model, 'first_name') ?>

                <?= $form->field($model, 'last_name') ?>

            </div>
            <div class="panel-footer text-right">
                <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success']) ?><br>
            </div>
            <?php \yii\widgets\ActiveForm::end(); ?>
        </div>
    </div>
</div>
