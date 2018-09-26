<?php

/**
 * @var yii\web\View 			$this
 * @var app\modules\user\Module 	$module
 */

$this->title = $title;

?>

<?= $this->render('/_alert', [
    'module' => $module,
]) ?>
