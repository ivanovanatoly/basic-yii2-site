<?php

/* @var $this \yii\web\View */
/* @var $content string */

?>

<?php $this->beginContent('@app/views/layouts/nifty/base.php'); ?>

<div id="container" class="cls-container">

    <div id="bg-overlay" class="bg-img" style="background-image: url(/img/register_background.png)"></div>

    <?php echo $content; ?>

</div>

<?php $this->endContent(); ?>
