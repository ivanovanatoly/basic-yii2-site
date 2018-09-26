<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use app\modules\user\models\User;

/**
 * @var yii\web\View 					$this
 * @var app\modules\user\models\User    $user
 * @var integer[] 		                $availableRoles
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

<?php foreach($availableRoles as $role) { ?>
    <div class="checkbox">
        <?php echo Html::hiddenInput(sprintf('roles[%s]', $role), 0); ?>
        <?php echo Html::checkbox(sprintf('roles[%s]', $role), $user->checkRole($role), [
            'id'    => 'role-' . $role,
            'class' => 'magic-checkbox',
        ]) ?>
        <label for="role-<?php echo $role?>"><?php echo User::getRoleTitle($role); ?></label>
    </div>
<?php } ?>

<div style="margin-top: 10px;">
    <?php echo Html::submitButton(Yii::t('user', 'Update'), ['class' => 'btn btn-block btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>

<?php $this->endContent() ?>
