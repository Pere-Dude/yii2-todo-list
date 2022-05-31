<?php
/* @var $this yii\web\View */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;

$this->title = 'Редактировать задачу';

$user_id = Yii::$app->user->id;
$admin = ArrayHelper::map(User::find()->where(['id' => $user_id, 'admin' => 1])->all(), 'id', 'fullName');
if ($admin) {
    //$users = ArrayHelper::map(User::find()->andWhere(['admin' => !1])->all(), 'id', 'fullName');
    //$users = array_merge($users, $admin);
    $users = ArrayHelper::map(User::find()->all(), 'id', 'fullName');
} else {
    $users = ArrayHelper::map(User::find()->where(['id' => $user_id])->all(), 'id', 'fullName');
}
?>

<div>
    <h1><?= Html::encode($this->title) ?></h1>


    <?php $form = ActiveForm::begin(); ?>
    <?php if ($admin || $model->creator == $user_id): ?>
        <?= $form->field($model, 'header')->input('string', ['required' => '']); ?>
        <?= $form->field($model, 'description')->input('string', ['required' => '']); ?>

        <?= $form->field($model, 'completion_date')->input('date', ['required' => '', 'value' => date('Y-m-d', $model->completion_date)]) ?>

        <?= $form->field($model, 'priority')->dropDownList(
            [
                '1' => 'низкий',
                '2' => 'средний',
                '3' => 'высокий'
            ]
        )
        ?>
        <?= $form->field($model, 'responsible')->dropDownList($users) ?>
    <?php endif; ?>

    <?php if (!$admin || $model->creator !== $user_id): ?>
        <div class = 'input-hidden'>
            <?= $form->field($model, 'completion_date')->input('date', ['required' => '', 'value' => date('Y-m-d', $model->completion_date)]) ?>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'status')->dropDownList(
        [
            '1' => 'к выполнению',
            '2' => 'выполняется',
            '3' => 'выполнена',
            '4' => 'отменена'
        ]
    ) ?>

    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
    <?php ActiveForm::end(); ?>
</div>