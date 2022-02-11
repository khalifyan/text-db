<?php

namespace app\controllers;

use app\db\DB;
use app\models\SignupForm;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
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
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
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
     */
    public function actionIndex()
    {
        return $this->actionSignin();
    }

    /**
     * Signin action.
     *
     * @return Response|string
     */
    public function actionSignin()
    {
        if (Yii::$app->session->get('user_id')) {
            return $this->redirect('/profile');
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/profile');
        }

        $model->password = '';
        return $this->render('login', compact('model'));
    }

    /**
     * Signs user up.
     *
     * @return Response|string
     * @throws Exception
     */
    public function actionSignup()
    {
        if (Yii::$app->session->get('user_id')) {
            return $this->redirect('/profile');
        }

        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'Спасибо за регистрацию');
            return $this->goHome();
        }

        return $this->render('signup', compact('model'));
    }

    /**
     * Signout action.
     *
     * @return Response
     */
    public function actionSignout(): Response
    {
        Yii::$app->session->destroy();
        Yii::$app->session->close();

        return $this->goHome();
    }

    /**
     * Profile action.
     *
     * @throws NotFoundHttpException
     */
    public function actionProfile(): string
    {
        if ($id = Yii::$app->session->get('user_id')) {
            $user = DB::getById($id);

            return $this->render('profile', compact('user'));
        }

        throw new NotFoundHttpException(403);
    }

}
