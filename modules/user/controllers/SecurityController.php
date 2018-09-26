<?php

namespace app\modules\user\controllers;

use app\modules\user\Finder;
use app\modules\user\models\Account;
use app\modules\user\models\LoginForm;
use app\modules\user\models\User;
use app\modules\user\Module;
use app\modules\user\traits\AjaxValidationTrait;
use Yii;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 */
class SecurityController extends Controller
{
    use AjaxValidationTrait;

    /** @var Finder */
    protected $finder;

    /**
     * @param string $id
     * @param Module $module
     * @param Finder $finder
     * @param array  $config
     */
    public function __construct($id, $module, Finder $finder, $config = [])
    {
        $this->finder = $finder;
        parent::__construct($id, $module, $config);
    }

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'actions' => ['login', 'auth', 'blocked'], 'roles' => ['?']],
                    ['allow' => true, 'actions' => ['login', 'auth', 'logout'], 'roles' => ['@']],
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

    /** @inheritdoc */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::className(),
                // if user is not logged in, will try to log him in, otherwise
                // will try to connect social account to user.
                'successCallback' => Yii::$app->user->isGuest
                    ? [$this, 'authenticate']
                    : [$this, 'connect'],
            ],
        ];
    }

    /**
     * Displays the login page.
     *
     * @return string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            $this->goHome();
        }

        $this->layout = '/nifty/background';

        /** @var LoginForm $model */
        $model = Yii::createObject(LoginForm::className());

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack(['backend/site/cabinet']);
        }

        return $this->render('login', [
            'model'  => $model,
            'module' => $this->module,
        ]);
    }

    /**
     * Logs the user out and then redirects to the homepage.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
     * @param ClientInterface $client
     */
    public function authenticate(ClientInterface $client)
    {
        $account = $this->finder->findAccount()->byClient($client)->one();

        if ($account === null) {
            $account = Account::create($client);
        }

        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t('user', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/user/security/login']);
            } else {
                Yii::$app->user->login($account->user, $this->module->rememberFor);
                $this->action->successUrl = Yii::$app->getUser()->getReturnUrl(['backend/site/cabinet']);
            }
        } else {
            $this->action->successUrl = $account->getConnectUrl();
        }
    }

    /**
     * Tries to connect social account to user.
     *
     * @param ClientInterface $client
     */
    public function connect(ClientInterface $client)
    {
        /** @var Account $account */
        $account = Yii::createObject(Account::className());
        $account->connectWithUser($client);
        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }
}
