<?php

namespace app\modules\user\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "profile".
 *
 * @property integer $user_id
 * @property string  $first_name
 * @property string  $last_name
 * @property User    $user
 *
 * Defined attributes:
 * @property string $fullName
 */
class Profile extends ActiveRecord
{
    /** @var \app\modules\user\Module */
    protected $module;

    /** @inheritdoc */
    public function init()
    {
        $this->module = Yii::$app->getModule('user');
    }

    /** @inheritdoc */
    public static function tableName()
    {
        return '{{%profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //first_name rules
            'firstNameRequired' => ['first_name', 'required'],
            'firstNameLength'   => ['first_name', 'string', 'max' => 255],
            'firstNameTrim'     => ['first_name', 'trim'],

            //last_name rules
            'lastNameRequired'  => ['last_name',  'required'],
            'lastNameLength'    => ['last_name',  'string', 'max' => 255],
            'lastNameTrim'      => ['last_name',  'trim'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'first_name'        => Yii::t('user', 'First Name'),
            'last_name'         => Yii::t('user', 'Last Name'),
        ];
    }

    /** @inheritdoc */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isAttributeChanged('gravatar_email')) {
                $this->setAttribute('gravatar_id', md5(strtolower($this->getAttribute('gravatar_email'))));
            }

            return true;
        }

        return false;
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne($this->module->modelMap['User'], ['id' => 'user_id']);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return ($this->last_name || $this->first_name) ? $this->last_name . ' ' . $this->first_name : Yii::t('*', 'Неопознанный енот');
    }
}
