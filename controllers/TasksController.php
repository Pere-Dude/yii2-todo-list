<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Tasks;

class TasksController extends Controller
{
    public function actionIndex()
    {
    }

    public function actionCreate()
    {
        $model = new Tasks();
        if ($model->load(Yii::$app->request->post())) {
            return $this->renderAjax('create', [
                'model' => $model
            ]);
        }
    }

    public function actionUpdate($id)
    {
        if (!ctype_digit($id)) {
            return $this->redirect(['page/index']);
        }
        $message = Feedback::findOne($id);
        if ($message->load(Yii::$app->request->post())) {
            if ($message->update()) {
                return $this->redirect(['page/index']);
            }
            Yii::$app->session->setFlash(
                'success',
                false
            );
        }
        return $this->render('feedback', ['message' => $message]);
    }
}
