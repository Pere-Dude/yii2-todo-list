<?php
namespace app\components;

use Yii;
use yii\helpers\Url;

class Init  extends \yii\base\Component  {

    public function toLogin() {
        if (\Yii::$app->getUser()->isGuest &&
            \Yii::$app->getRequest()->url !== Url::to('web\site\login')
        ) {
            \Yii::$app->getResponse()->redirect('\web\site\login');
        }
    }
}