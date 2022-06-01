<?php

namespace app\controllers;

use app\models\Tasks;
use app\models\TasksSearch;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use yii\helpers\ArrayHelper;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user_id = Yii::$app->user->id;
        $searchModel = new TasksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        $users = ArrayHelper::map(User::find()->all(), 'id', 'fullName');

        $isAdmin = User::find()->where(['id' => $user_id, 'admin' => 1])->one();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'users' => $users,
            'isAdmin' => $isAdmin
        ]);
    }

    public function actionInsert()
    {
        $model = new Tasks();
        if ($model->load(Yii::$app->request->post())) {
            $model->creator = Yii::$app->user->id;
            $model->completion_date = strtotime($model->completion_date);
            $model->created_at = time();
            $model->updated_at = time();
            $model->save();
            return $this->redirect(['/']);
        } else {
            return $this->renderAjax('insert', ['model' => $model]);
        }
    }

    public function actionUpdate($id)
    {
        $model = Tasks::findOne($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->completion_date !== '') {
                $model->completion_date = strtotime($model->completion_date);
            }
            $model->updated_at = time();
            $model->save();
            return $this->redirect(['/']);
        }
        return $this->renderAjax('update', ['model' => $model]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $this->actionAddUsers();
        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionAddUsers()
    {
        $admin = User::find()->where(['username' => 'admin'])->one();
        if (empty($admin)) {
            $user = new User();
            $user->username = 'admin';
            $user->name = 'Руководитель';
            $user->surname = '';
            $user->patronymic = '';
            $user->admin = 1;
            $user->setPassword('admin');
            $user->generateAuthKey();
            if ($user->save()) {
                echo "Руководитель создан<br>";
            }
        }

        $user_test = User::find()->where(['username' => 'user'])->one();
        if (empty($user_test)) {
            $user = new User();
            $user->username = 'user';
            $user->name = 'Пользователь';
            $user->surname = '';
            $user->patronymic = '';
            $user->admin = 0;
            $user->setPassword('user');
            $user->generateAuthKey();
            if ($user->save()) {
                echo "Пользователь создан<br>";
            }
        }
    }

}
