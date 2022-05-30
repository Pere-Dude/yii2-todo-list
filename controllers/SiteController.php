<?php

namespace app\controllers;

use app\models\Tasks;
use Yii;
use yii\base\BaseObject;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\User;
use yii\data\Pagination;

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
        $rows = Tasks::find();
        $DataProvider = new ActiveDataProvider(
            ['query' => Tasks::find(),
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
                    'defaultOrder' => ['updated_at' => SORT_DESC]
                ]
            ]
        );
        // $searchModel = new tasksSearch();
        $pagination = new Pagination([
            'defaultPageSize' => 20, // количество записей на странице
            'totalCount' => $rows->count(), // количество записей в таблице
        ]);
        $tasks_rows = $rows->offset($pagination->offset) // отступ постраничной навигации
        ->limit($pagination->limit) // ограничение размера выборки
        ->all();
        return $this->render('index', ['rows' => $tasks_rows, 'pagination' => $pagination, 'DataProvider' => $DataProvider]);
    }

    public function actionInsert()
    {
        $model = new Tasks();
        if ($model->load(Yii::$app->request->post())) {
            $model->creator = Yii::$app->user->id;
            $model->completion_date = strtotime($model->completion_date);
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
            $model->completion_date = strtotime($model->completion_date);
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

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionAddAdmin()
    {
        $model = User::find()->where(['username' => 'admin'])->one();
        if (empty($model)) {
            $user = new User();
            $user->username = 'admin';
            $user->name = 'Руководитель';
            $user->surname = '';
            $user->patronymic = '';
            $user->admin = 1;
            $user->setPassword('admin');
            $user->generateAuthKey();
            if ($user->save()) {
                echo 'good';
            }
        }
    }


}
