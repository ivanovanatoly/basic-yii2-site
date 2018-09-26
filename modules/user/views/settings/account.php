<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this  yii\web\View
 * @var $form  yii\widgets\ActiveForm
 * @var $model app\modules\user\models\SettingsForm
 */

$this->title = Yii::t('user', 'Account settings');
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
            <?php $form = ActiveForm::begin([
                'id'          => 'account-form',
                'options'     => ['class' => 'form-horizontal'],
                'fieldConfig' => [
                    'template'     => "{label}\n<div class=\"col-lg-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-lg-9\">{error}\n{hint}</div>",
                    'labelOptions' => ['class' => 'col-lg-3 control-label'],
                ],
                'enableAjaxValidation'   => true,
                'enableClientValidation' => false,
            ]); ?>
            <div class="panel-body">

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'new_password')->passwordInput() ?>

                <hr />

                <?= $form->field($model, 'current_password')->passwordInput() ?>

            </div>
            <div class="panel-footer text-right">
                <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success']) ?><br>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
