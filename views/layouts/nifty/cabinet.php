<?php

/* @var $this \yii\web\View */
/* @var $content string */

$user = Yii::$app->user->identity;

?>

<?php $this->beginContent('@app/views/layouts/nifty/base.php'); ?>

    <div id="container" class="effect mainnav-lg">

        <?php echo $this->render('blocks/header'); ?>

        <div class="boxed">

            <div id="content-container">

                <div id="page-content">

                    <?php echo \yii\widgets\Breadcrumbs::widget([
                        'homeLink' => ['label' => 'Главная', 'url' => '/cabinet'],
                        'links'    => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]); ?>

                    <div class="row">
                        <div class="col-xs-12">
                            <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                                <?php if (in_array($type, ['success', 'danger', 'warning', 'info'])): ?>
                                    <div class="alert alert-<?= $type ?>">
                                        <?= $message ?>
                                    </div>
                                <?php endif ?>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <?php echo $content; ?>

                </div>

            </div>

            <?php echo $this->render('blocks/nav', ['user' => $user]); ?>

        </div>


        <?php echo $this->render('blocks/footer'); ?>

        <button class="scroll-top btn">
            <i class="pci-chevron chevron-up"></i>
        </button>

    </div>

<?php $this->endContent(); ?>
