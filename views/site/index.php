<?php

/** @var yii\web\View $this */

use app\models\User;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap4\Modal;

$users = User::getUsersArray();
$this->title = 'ToDo List';
$today_date = strtotime(date('Y-m-d'));
?>
<?php if (Yii::$app->user->isGuest == true): ?>
    <?php \app\components\Init::toLogin(); ?>
<?php else: ?>
    <h1><?= $this->title; ?></h1>

    <?= Html::beginForm('', 'get'); ?>
    <div class="form-group-filter">
        <?= Html::label('Диапазон дат', '-ID', ['class' => 'control-label']) ?>
        <?= Html::dropDownList('date_filter', '',
            [
                'today' => 'На сегодня',
                'week' => 'На неделю',
                'future' => 'На будущее'
            ],
            ['class' => 'form-control',]); ?>
        <?php if (User::isAdmin()): ?>
            <?= Html::label('Ответственные', '', ['class' => 'control-label']) ?>
            <?= Html::dropDownList('TasksSearch[responsible]', '', $users, ['class' => 'form-control']); ?>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-success']) ?>
    </div>
    <?php Html::endForm(); ?>

    <?php if ($_GET["TasksSearch"] || $_GET["date_filter"]): ?>
        <a href="/" class="btn btn-primary reset_filters">Сбросить фильтр</a>
    <?php endif; ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'pager' => [
            'options' => [
                'tag' => 'ul',
                'class' => 'pagination pagination-sm',
            ]
        ],
        'columns' => [
            [
                'attribute' => 'header',
                'value' => function ($data) {
                    if (($data->completion_date + (24 * 60 * 60)) < time() && $data->status !== 3) {
                        return "<span class='grid-cell-red'>" . $data->header . "</span>";
                    } else if ($data->status == 3) {
                        return "<span class='grid-cell-green'>" . $data->header . "</span>";
                    } else {
                        return "<span class='grid-cell-gray'>" . $data->header . "</span>";
                    }

                },
                'format' => 'html',
                'filter' => false
            ],
            [
                'attribute' => 'priority',
                'value' => function ($data) {
                    switch ($data->priority) {
                        case 1:
                            $priority = 'низкий';
                            break;
                        case 2:
                            $priority = 'средний';
                            break;
                        case 3:
                            $priority = 'высокий';
                            break;
                    }
                    return $priority;
                },
                'filter' => false
            ],
            [
                'attribute' => 'completion_date',
                'format' => ['datetime', 'php:d.m.Y'],
                'filter' => false,
            ],
            [
                'attribute' => 'responsible',
                'value' => function ($data) {
                    $user = User::find()->where("id = $data->responsible")->one();
                    return $user['fullName'];
                },
                'filter' => false
            ],
            [
                'attribute' => 'status',
                'value' => function ($data) {
                    switch ($data->status) {
                        case 1:
                            $status = 'к выполнению';
                            break;
                        case 2:
                            $status = 'выполняется';
                            break;
                        case 3:
                            $status = 'выполнена';
                            break;
                        case 4:
                            $status = 'отменена';
                            break;
                        default:
                            $status = 'к выполнению';
                            break;
                    }
                    return $status;
                },
                'filter' => false
            ],
            [
                'label' => '',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::button("Редактировать",
                        ['value' => Url::to("/web/site/update?id=$data->id"),
                            'id' => 'modalButtonUpdate' . $data->id,
                            'class' => 'btn btn-primary not-button'
                        ]
                    );
                }
            ],
        ],
    ]); ?>

    <?php
    Modal::begin([
        'id' => 'modalUpdate',
        'size' => 'modal-lg'
    ]);
    echo "<div id='modalContent1'></div>";
    Modal::end();
    ?>

    <?=
    Html::button("Новая задача", ['value' => Url::to('/web/site/insert'), 'class' => 'btn btn-primary', 'id' => 'modalButton'])
    ?>
    <?php
    Modal::begin([
        'id' => 'modal',
        'size' => 'modal-lg'
    ]);
    echo "<div id='modalContent'></div>";
    Modal::end();
    ?>


<?php endif;
?>