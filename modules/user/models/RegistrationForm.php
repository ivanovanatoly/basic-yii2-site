<?php

namespace app\modules\user\models;

use app\modules\user\Module;
use Yii;
use yii\base\Model;

/**
 * Registration form collects user input on registration process, validates it and creates new User model.
 */
class RegistrationForm extends Model
{
    /**
     * @var string User email address
     */
    public $email;

    /**
     * @var string First Name
     */
    public $first_name;

    /**
     * @var string Last Name
     */
    public $last_name;

    /**
     * @var string Password
     */
    public $password;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->module = Yii::$app->getModule('user');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $user = $this->module->modelMap['User'];

        return [
            // first_name rules
            'firstNameLength'   => ['first_name', 'string', 'min' => 3, 'max' => 255],
            'firstNameTrim'     => ['first_name', 'filter', 'filter' => 'trim'],
            'firstNameRequired' => ['first_name', 'required'],

            // last_name rules
            'lastNameLength'   => ['last_name', 'string', 'min' => 3, 'max' => 255],
            'lastNameTrim'     => ['last_name', 'filter', 'filter' => 'trim'],
            'lastNameRequired' => ['last_name', 'required'],

            // email rules
            'emailTrim'     => ['email', 'filter', 'filter' => 'trim'],
            'emailRequired' => ['email', 'required'],
            'emailPattern'  => ['email', 'email'],
            'emailUnique'   => [
                'email',
                'unique',
                'targetClass' => $user,
                'message' => Yii::t('user', 'This email address has already been taken')
            ],
            // password rules
            'passwordRequired' => ['password', 'required', 'skipOnEmpty' => $this->module->enableGeneratingPassword],
            'passwordLength'   => ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'email'      => Yii::t('user', 'Email'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name'  => Yii::t('user', 'Last Name'),
            'password'   => Yii::t('user', 'Password'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return 'register-form';
    }

    /**
     * Registers a new user account. If registration was successful it will set flash message.
     *
     * @return bool
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        /** @var User $user */
        $user = Yii::createObject(User::className());
        $user->setScenario('register');
        $this->loadAttributes($user);

        if (!$user->register()) {
            return false;
        }

        Yii::$app->session->setFlash(
            'info',
            Yii::t('user', 'Your account has been created and a message with further instructions has been sent to your email')
        );

        return true;
    }

    /**
     * Loads attributes to the user model. You should override this method if you are going to add new fields to the
     * registration form. You can read more in special guide.
     *
     * By default this method set all attributes of this model to the attributes of User model, so you should properly
     * configure safe attributes of your User model.
     *
     * @param User $user
     */
    protected function loadAttributes(User $user)
    {
        $user->setAttributes($this->attributes);
        /** @var Profile $profile */
        $profile = Yii::createObject(Profile::className());
        $profile->setAttributes([
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
        ]);
        $user->setProfile($profile);
    }
}
