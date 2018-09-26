<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var yii\web\View 					$this
 * @var app\modules\user\models\User 		$user
 * @var app\modules\user\models\Profile 	$profile
 */

?>

<?php $this->beginContent('@app/modules/user/views/admin/update.php', ['user' => $user]) ?>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'enableAjaxValidation' => true,
    'enableClientValidation' => false,
    'fieldConfig' => [
        'horizontalCssClasses' => [
            'wrapper' => 'col-sm-9',
        ],
    ],
]); ?>

<?php echo $form->field($profile, 'first_name') ?>
<?php echo $form->field($profile, 'last_name') ?>


<div class="form-group">
    <div class="col-lg-offset-3 col-lg-9">
        <?php echo Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
