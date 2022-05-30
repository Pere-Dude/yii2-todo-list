<?php
/* @var $this yii\web\View */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\models\User;

$this->title = 'Добавить задачу';

$user_id = Yii::$app->user->id;
$admin = ArrayHelper::map(User::find()->where(['id' => $user_id, 'admin' => 1])->all(), 'id', 'fullName');
if ($admin) {
    $users = ArrayHelper::map(User::find()->andWhere(['admin' => !1])->all(), 'id', 'fullName');
    $users = array_merge($users, $admin);
} else {
    $users = ArrayHelper::map(User::find()->where(['id' => $user_id])->all(), 'id', 'fullName');
}
?>

<div id='modalContent'>
    <div class="page-feedback">
        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'header')->input('string', ['required' => '']); ?>
        <?= $form->field($model, 'description')->input('string', ['required' => '']); ?>

        <?= $form->field($model, 'completion_date')->input('date', ['required' => '']) ?>

        <?= $form->field($model, 'priority')->dropDownList(
            [
                '1' => 'низкий',
                '2' => 'средний',
                '3' => 'высокий'
            ]
        )
        ?>
        <?= $form->field($model, 'status')->dropDownList(
            [
                '1' => 'к выполнению',
                '2' => 'выполняется',
                '3' => 'выполнена',
                '4' => 'отменена'
            ]
        ) ?>
        <?= $form->field($model, 'responsible')->dropDownList($users) ?>

        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']); ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>