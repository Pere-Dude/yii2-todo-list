<?php

/** @var yii\web\View $this */

use app\models\User;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use \app\models\Tasks;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap4\Modal;
use yii\helpers\ArrayHelper;


$this->title = 'ToDo List';
?>
<?php if (Yii::$app->user->isGuest == true): ?>
    <?php \app\components\Init::toLogin(); ?>
<?php else: ?>

<h1><?= $this->title = 'ToDo List'; ?></h1>

    <?= GridView::widget([
        'dataProvider' => $DataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'header',
                'value' => function ($data) {
                    if ($data->completion_date < time() && $data->status !== 3) {
                        return "<span class='grid-cell-red'>" . $data->header . "</span>";
                    } else if ($data->status == 3) {
                        return "<span class='grid-cell-green'>" . $data->header . "</span>";
                    } else {
                        return "<span class='grid-cell-gray'>" . $data->header . "</span>";
                    }

                },
                'format' => 'html',

            ],
            'priority',
            [
                'attribute' => 'completion_date',
                'format' => ['datetime', 'php:d.m.Y'],
            ],
            [
                'attribute' => 'responsible',
                'value' => function ($data) {
                    $user = User::find()->where("id = $data->responsible")->one();
                    return $user['fullName'];
                },
                //'filter' => ArrayHelper::map(User::find()->all(), 'id', 'fullName'),
                //'filterInputOptions' => ['class' => 'form-control form-control-sm']
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
                }
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
    Html::button("Создать задачу", ['value' => Url::to('/web/site/insert'), 'class' => 'btn btn-primary', 'id' => 'modalButton'])
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

//echo User::getRole();
?>