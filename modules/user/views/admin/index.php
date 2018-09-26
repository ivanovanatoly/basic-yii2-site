<?php

use app\modules\user\models\UserSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\web\View;
use yii\widgets\Pjax;
use app\assets\Select2Asset;
use app\modules\user\models\User;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 * @var array $roles
 */

$this->title = Yii::t('user', 'Manage users');
$this->params['breadcrumbs'][] = $this->title;

Select2Asset::register($this);
$this->registerJs('$("select").select2();');
$this->registerJs('$(document).on("pjax:success", function() {
    $("select").select2();
});');

?>

<div class="panel">

    <div class="panel-heading">
        <div class="panel-control">
            <?php echo Html::a('Создать пользователя', ['/user/admin/create'], ['class' => 'btn btn-primary btn-labeled ti-plus']); ?>
        </div>
        <h3 class="panel-title"><?php echo Html::encode($this->title) ?></h3>
    </div>

    <?php Pjax::begin() ?>

    <?php echo GridView::widget([
        'dataProvider' 	=> $dataProvider,
        'filterModel'  	=> $searchModel,
        'layout'  		=> "{items}\n{pager}",
        'columns'       => [
            'username',
            'email:email',
            [
                'attribute' => 'role',
                'label'     => Yii::t('user', 'Роль'),
                'value'     => function (User $model) {
                    return implode(', ', $model->getRoles());
                },
                'filter'             => $roles,
                'filterInputOptions' => ['prompt' => 'Выбор роли', 'style' => 'width: 100%;'],
                'headerOptions'      => ['style' => 'width: 80px;']
            ],
            [
                'attribute' => 'registration_ip',
                'value'     => function ($model) {
                    return $model->registration_ip == null
                        ? '<span class="not-set">' . Yii::t('user', '(not set)') . '</span>'
                        : $model->registration_ip;
                },
                'format'    => 'html',
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    if (extension_loaded('intl')) {
                        return Yii::t('user', '{0, date, MMMM dd, YYYY HH:mm}', [$model->created_at]);
                    } else {
                        return date('Y-m-d G:i:s', $model->created_at);
                    }
                },
                'filter' => DatePicker::widget([
                    'model'      => $searchModel,
                    'attribute'  => 'created_at',
                    'dateFormat' => 'php:Y-m-d',
                    'options'    => [
                        'class'    => 'form-control',
                    ],
                ]),
            ],
            [
                'header' => Yii::t('user', 'Confirmation'),
                'value'  => function ($model) {
                    if ($model->isConfirmed) {
                        return '<div class="text-center"><span class="text-success">' . Yii::t('user', 'Confirmed') . '</span></div>';
                    } else {
                        return Html::a(Yii::t('user', 'Confirm'), ['confirm', 'id' => $model->id], [
                            'class' => 'btn btn-xs btn-success btn-block',
                            'data-method' => 'post',
                            'data-confirm' => Yii::t('user', 'Are you sure you want to confirm this user?'),
                        ]);
                    }
                },
                'format'  => 'raw',
                'visible' => Yii::$app->getModule('user')->enableConfirmation,
            ],
            [
                'class'    => 'yii\grid\ActionColumn',
                'template' => '{update}',
            ],
        ],
    ]); ?>

    <?php Pjax::end() ?>
</div>
