<?php

use yii\helpers\Html;
use app\assets\NiftyAsset;

/* @var $this \yii\web\View */
/* @var $content string */

NiftyAsset::register($this);

?>

<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language; ?>">
<head>
    <meta charset="<?= Yii::$app->charset; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php echo Html::csrfMetaTags(); ?>
    <?php $this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'href' => '/favicon.ico']); ?>
    <title><?php echo Html::encode($this->title); ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody(); ?>
<?php echo $content; ?>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
