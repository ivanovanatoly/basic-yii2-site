<?php

namespace app\components\upload;

use Closure;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\UploadedFile;

/**
 * UploadBehavior automatically uploads file and fills the specified attribute
 * with a value of the name of the uploaded file.
 *
 * To use UploadBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use app\components\UploadBehavior;
 *
 * function behaviors()
 * {
 *     return [
 *         [
 *             'class' => UploadBehavior::className(),
 *             'attribute' => 'file',
 *             'scenarios' => ['insert', 'update'],
 *             'path' => '@webroot/upload/{id}',
 *             'url' => '@web/upload/{id}',
 *         ],
 *     ];
 * }
 * ```
 */
class UploadBehavior extends Behavior
{
    /**
     * @event Event an event that is triggered after a file is uploaded.
     */
    const EVENT_AFTER_UPLOAD = 'afterUpload';

    /**
     * @var string the attribute which holds the attachment.
     */
    public $attribute;
    /**
     * @var array the scenarios in which the behavior will be triggered
     */
    public $scenarios = [];
    /**
     * @var string the base path or path alias to the directory in which to save files.
     */
    public $path;
    /**
     * @var string the base URL or path alias for this file
     */
    public $url;
    /**
     * @var bool Getting file instance by name
     */
    public $instanceByName = false;
    /**
     * @var boolean|callable generate a new unique name for the file
     * set true or anonymous function takes the old filename and returns a new name.
     * @see self::generateFileName()
     */
    public $generateNewName = true;
    /**
     * @var boolean If `true` current attribute file will be deleted
     */
    public $unlinkOnSave = true;
    /**
     * @var boolean If `true` current attribute file will be deleted after model deletion.
     */
    public $unlinkOnDelete = true;
    /**
     * @var boolean $deleteTempFile whether to delete the temporary file after saving.
     */
    public $deleteTempFile = true;

    /**
     * @var UploadedFile the uploaded file instance.
     */
    protected $_file;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->attribute === null) {
            throw new InvalidConfigException('The "attribute" property must be set.');
        }
        if ($this->path === null) {
            throw new InvalidConfigException('The "path" property must be set.');
        }
        if ($this->url === null) {
            throw new InvalidConfigException('The "url" property must be set.');
        }
    }

    /**
     * Returns behavior instance for specified class and attribute
     *
     * @param ActiveRecord $model
     * @param string $attribute
     * @return static
     */
    public static function getInstance(ActiveRecord $model, $attribute)
    {
        foreach ($model->behaviors as $behavior) {
            if ($behavior instanceof self && $behavior->attribute == $attribute)
                return $behavior;
        }
        throw new InvalidCallException('Missing behavior for attribute ' . VarDumper::dumpAsString($attribute));
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * This method is invoked before validation starts.
     */
    public function beforeValidate()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if (in_array($model->scenario, $this->scenarios)) {
            if (($file = $model->getAttribute($this->attribute)) instanceof UploadedFile) {
                $this->_file = $file;
            } else {
                if ($this->instanceByName === true) {
                    $this->_file = UploadedFile::getInstanceByName($this->attribute);
                } else {
                    $this->_file = UploadedFile::getInstance($model, $this->attribute);
                }
            }
            if ($this->_file instanceof UploadedFile) {
                $this->_file->name = $this->getFileName($this->_file);
                $model->setAttribute($this->attribute, $this->_file);
            }
        }
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     */
    public function beforeSave()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if (in_array($model->scenario, $this->scenarios)) {
            if ($this->_file instanceof UploadedFile) {
                if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                    if ($this->unlinkOnSave === true) {
                        $this->delete($this->attribute, true);
                    }
                }
                $model->setAttribute($this->attribute, $this->_file->name);
            } else {
                // Protect attribute
                unset($model->{$this->attribute});
            }
        } else {
            if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                if ($this->unlinkOnSave === true) {
                    $this->delete($this->attribute, true);
                }
            }
        }
    }

    /**
     * This method is called at the end of inserting or updating a record.
     * @throws \yii\base\InvalidParamException
     */
    public function afterSave()
    {
        if ($this->_file instanceof UploadedFile) {
            $path = $this->getUploadPath($this->attribute);
            $this->save($this->_file, $path);
            $this->afterUpload();
        }
    }

    /**
     * This method is invoked after deleting a record.
     */
    public function afterDelete()
    {
        $attribute = $this->attribute;
        if ($this->unlinkOnDelete && $attribute) {
            $this->delete($attribute);
        }
    }

    /**
     * Returns file path for the attribute.
     * @param string $attribute
     * @param boolean $old
     * @return string|null the file path.
     */
    public function getUploadPath($attribute, $old = false)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $behavior = static::getInstance($this->owner, $attribute);
        $path = $behavior->resolvePath($behavior->path);
        $fileName = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;

        return $fileName ? $path . '/' . $fileName : null;
    }

    /**
     * Returns file url for the attribute.
     * @param string $attribute
     * @return string|null
     */
    public function getUploadUrl($attribute)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $behavior = static::getInstance($this->owner, $attribute);
        $url = $behavior->resolvePath($behavior->url);
        $fileName = $model->getOldAttribute($attribute);

        return $fileName ? Yii::$app->storage->getUrl($url . '/' . $fileName) : null;
    }

    /**
     * Replaces all placeholders in path variable with corresponding values.
     */
    protected function resolvePath($path)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        return preg_replace_callback('/{([^}]+)}/', function ($matches) use ($model) {
            $name = $matches[1];
            $attribute = ArrayHelper::getValue($model, $name);
            if (is_string($attribute) || is_numeric($attribute)) {
                return $attribute;
            } else {
                return $matches[0];
            }
        }, $path);
    }

    /**
     * Saves the uploaded file.
     * @param UploadedFile $file the uploaded file instance
     * @param string $path the file path used to save the uploaded file
     * @return boolean true whether the file is saved successfully
     */
    protected function save($file, $path)
    {
        return Yii::$app->storage->save($file->tempName, $path);
    }

    /**
     * Deletes old file.
     * @param string $attribute
     * @param boolean $old
     */
    protected function delete($attribute, $old = false)
    {
        $path = $this->getUploadPath($attribute, $old);
        if (!empty($path) && Yii::$app->storage->fileExists($path)) {
            Yii::$app->storage->delete($path);
        }
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    protected function getFileName($file)
    {
        if ($this->generateNewName) {
            return $this->generateNewName instanceof Closure
                ? call_user_func($this->generateNewName, $file->name, $file->extension)
                : $this->generateFileName($file->name, $file->extension);
        } else {
            return $this->sanitize($file->name);
        }
    }

    /**
     * Replaces characters in strings that are illegal/unsafe for filename.
     *
     * #my*  unsaf<e>&file:name?".png
     *
     * @param string $filename the source filename to be "sanitized"
     * @return boolean string the sanitized filename
     */
    public static function sanitize($filename)
    {
        return str_replace([' ', '"', '\'', '&', '/', '\\', '?', '#'], '-', $filename);
    }

    protected function generateFileName($filename, $extension)
    {
        return uniqid() . '.' . $extension;
    }

    /**
     * This method is invoked after uploading a file.
     * The default implementation raises the [[EVENT_AFTER_UPLOAD]] event.
     * You may override this method to do postprocessing after the file is uploaded.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    protected function afterUpload()
    {
        $this->owner->trigger(self::EVENT_AFTER_UPLOAD);
    }
}
