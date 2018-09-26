<?php

/* @var $this \yii\web\View */

?>

<header id="navbar">
    <div id="navbar-container" class="boxed">

        <!--Brand logo & name-->
        <!--================================-->
        <div class="navbar-header">
            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['frontend/site/index']); ?>" class="navbar-brand">
                <img src="/favicon.ico" alt="Nifty Logo" class="brand-icon">
                <div class="brand-title">
                    <span class="brand-text">Basic</span>
                </div>
            </a>
        </div>
        <!--================================-->
        <!--End brand logo & name-->


        <!--Navbar Dropdown-->
        <!--================================-->
        <div class="navbar-content clearfix">
            <ul class="nav navbar-top-links pull-left">

                <!--Navigation toogle button-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <li class="tgl-menu-btn">
                    <a class="mainnav-toggle" href="#">
                        <i class="ti-view-list icon-lg"></i>
                    </a>
                </li>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End Navigation toogle button-->

            </ul>
            <ul class="nav navbar-top-links pull-right">

                <!--User dropdown-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <li id="dropdown-user" class="dropdown">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
                                <span class="pull-right">
                                    <!-- You may use image instead of an icon.
                                    <!--<img class="img-circle img-user media-object" src="img/av1.png" alt="Profile Picture">-->
                                    <i class="ti-face-smile ic-user"></i>
                                </span>
                        <div class="username hidden-xs"><?php echo isset($user->profile) ? $user->profile->getFullName() : Yii::t('*', 'Неопознанный енот'); ?></div>
                    </a>


                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right panel-default">

                        <!-- User dropdown menu -->
                        <ul class="head-list bord-btm">
                            <li>
                                <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['/user/settings/account']); ?>">
                                    <i class="ti-user icon-fw icon-lg"></i> <?php echo Yii::t('*', 'Аккаунт'); ?>
                                </a>
                            </li>
                        </ul>

                        <!-- Dropdown footer -->
                        <div class="pad-all text-right">
                            <a href="<?php echo Yii::$app->getUrlManager()->createUrl(['/user/security/logout']); ?>" data-method="post" class="btn btn-primary">
                                <i class="ti-unlock icon-fw"></i> <?php echo Yii::t('*', 'Выйти'); ?>
                            </a>
                        </div>

                    </div>
                </li>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End user dropdown-->

            </ul>
        </div>
        <!--================================-->
        <!--End Navbar Dropdown-->

    </div>
</header>
