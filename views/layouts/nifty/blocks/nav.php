<?php

use app\modules\user\models\User;

/* @var $this \yii\web\View */
/* @var $user app\modules\user\models\User */

?>

<nav id="mainnav-container">
    <div id="mainnav">
        <div id="mainnav-menu-wrap">
            <div class="nano">
                <div class="nano-content">

                    <div id="mainnav-profile" class="mainnav-profile">
                        <div class="profile-wrap">
                            <div class="pad-btm">
                                <img class="img-circle img-sm img-border" src="/img/profile-photos/1.png" alt="Profile Picture">
                            </div>
                            <a href="#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                                            <span class="pull-right dropdown-toggle">
                                                <i class="dropdown-caret"></i>
                                            </span>
                                <p class="mnp-name"><?php echo isset($user->profile) ? $user->profile->getFullName() : 'Неопознанный енот'; ?></p>
                                <span class="mnp-desc"><?php echo $user->email; ?></span>
                            </a>
                        </div>
                        <div id="profile-nav" class="collapse list-group bg-trans">
                            <a href="#" class="list-group-item">
                                <i class="ti-medall icon-lg icon-fw"></i> Link 1
                            </a>
                            <a href="#" class="list-group-item">
                                <i class="ti-paint-roller icon-lg icon-fw"></i> Link 2
                            </a>
                            <a href="#" class="list-group-item">
                                <i class="ti-heart icon-lg icon-fw"></i> Link 3
                            </a>
                        </div>
                    </div>

                    <?php echo \app\widgets\Nav::widget([
                        'items' => [
                            [
                                'label' => 'Профиль',
                                'url' => ['/user/settings/profile'],
                                'icon' => 'ti-user',
                            ],
                            [
                                'label' => 'Пользователи',
                                'url' => ['/user/admin/index'],
                                'icon' => 'ti-wheelchair',
                                'visible' => $user->checkRole(User::ROLE_SUPER_ADMIN)
                            ],
                        ],
                    ]); ?>

                </div>
            </div>
        </div>
    </div>
</nav>
