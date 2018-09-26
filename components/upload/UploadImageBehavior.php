<?php

namespace app\components\upload;

use Imagine\Image\ManipulatorInterface;
use Yii;
use Closure;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\imagine\Image;

/**
 * UploadImageBehavior automatically uploads image, creates thumbnails and fills
 * the specified attribute with a value of the name of the uploaded image.
 *
 * To use UploadImageBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use app\components\UploadImageBehavior;
 *
 * function behaviors()
 * {
 *     return [
 *         [
 *             'class' => UploadImageBehavior::className(),
 *             'attribute' => 'file',
 *             'scenarios' => ['insert', 'update'],
 *             'placeholder' => '@app/modules/user/assets/images/userpic.jpg',
 *             'path' => '@webroot/upload/{id}/images',
 *             'url' => '@web/upload/{id}/images',
 *             'thumbPath' => '@webroot/upload/{id}/images/thumb',
 *             'thumbUrl' => '@web/upload/{id}/images/thumb',
 *             'thumbs' => [
 *                   'thumb' => ['width' => 400, 'quality' => 90],
 *                   'preview' => ['width' => 200, 'height' => 200],
 *              ],
 *         ],
 *     ];
 * }
 * ```
 */
class UploadImageBehavior extends UploadBehavior
{
    /**
     * @var string|callable
     */
    public $placeholder;
    /**
     * @var boolean
     */
    public $createThumbsOnSave = true;
    /**
     * @var boolean
     */
    public $createThumbsOnRequest = false;
    /**
     * @var array the thumbnail profiles
     * - `width`
     * - `height`
     * - `quality`
     */
    public $thumbs = [
        'thumb' => ['width' => 200, 'height' => 200, 'quality' => 90],
    ];
    /**
     * @var string|null
     */
    public $thumbPath;
    /**
     * @var string|null
     */
    public $thumbUrl;

    protected $_imagePath;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->createThumbsOnSave) {
            if ($this->thumbPath === null) {
                $this->thumbPath = $this->path;
            }
            if ($this->thumbUrl === null) {
                $this->thumbUrl = $this->url;
            }

            foreach ($this->thumbs as $config) {
                $width = ArrayHelper::getValue($config, 'width');
                $height = ArrayHelper::getValue($config, 'height');
                if ($height < 1 && $width < 1) {
                    throw new InvalidConfigException(sprintf(
                        'Length of either side of thumb cannot be 0 or negative, current size ' .
                        'is %sx%s', $width, $height
                    ));
                }
            }
        }
    }

    public function attachImage($attribute, $imagePath)
    {
        $behavior = static::getInstance($this->owner, $attribute);
        $behavior->_imagePath = Yii::getAlias($imagePath);
    }

    public function attachB64Image($attribute, $b64Image)
    {
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $b64Image));
        $imagePath = Yii::$app->getRuntimePath() . '/cropper/';
        if (!is_dir($imagePath)) {
            FileHelper::createDirectory($imagePath);
        }
        $imagePath .= uniqid() . '.jpg';
        file_put_contents($imagePath, $image);
        $behavior = static::getInstance($this->owner, $attribute);
        $behavior->_imagePath = Yii::getAlias($imagePath);
    }

    public function getPlaceholder()
    {
        if ($this->placeholder instanceof Closure) {
            return call_user_func($this->placeholder, $this->owner);
        } else {
            return $this->placeholder;
        }
    }

    /**
     * @inheritdoc
     */
    protected function afterUpload()
    {
        parent::afterUpload();
        if ($this->createThumbsOnSave) {
            $this->createThumbs();
        }
    }

    public function beforeSave()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if (!empty($b64 = $model->{$this->attribute . '_b64'})) {
            $this->attachB64Image($this->attribute, $b64);
        }
        if (!empty($this->_imagePath)) {
            $info = pathinfo($this->_imagePath);
            if($this->generateNewName instanceof Closure) {
                $imageName = call_user_func($this->generateNewName, $info['filename'], $info['extension']);
            } else {
                $imageName = $this->generateFileName($info['filename'], $info['extension']);
            }
            $model->setAttribute($this->attribute, $imageName);
            if ($this->unlinkOnSave === true) {
                $this->delete($this->attribute, true);
            }
        } else {
            parent::beforeSave();
        }
    }

    public function afterSave()
    {
        if (!empty($this->_imagePath)) {
            $path = $this->getUploadPath($this->attribute);
            Yii::$app->storage->save($this->_imagePath, $path);
            $this->createThumbs($this->_imagePath);
        } else {
            parent::afterSave();
        }
    }

    protected function createThumbs($path = null)
    {
        if (empty($path) && !empty($this->_file)) {
            $path = $this->_file->tempName;
        }
        if (empty($path)) {
            $path = $this->getUploadPath($this->attribute);
            if (!file_exists($path)) {
                //TODO
                /*$result = $client->getObject(array(
                    'Bucket' => $bucket,
                    'Key'    => 'data.txt',
                    'SaveAs' => '/tmp/data.txt'
                ));*/
            }
        }
        foreach ($this->thumbs as $profile => $config) {
            $thumbPath = $this->getThumbUploadPath($this->attribute, $profile);
            if ($thumbPath !== null) {
                if (!Yii::$app->storage->fileExists($thumbPath)) {
                    $this->generateImageThumb($config, $path, $thumbPath);
                }
            }
        }
        @unlink($path);
    }

    /**
     * @param string $attribute
     * @param string $profile
     * @param boolean $old
     * @return string
     */
    public function getThumbUploadPath($attribute, $profile = 'thumb', $old = false)
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $path = $this->resolvePath($this->thumbPath);
        $attribute = ($old === true) ? $model->getOldAttribute($attribute) : $model->$attribute;
        if (!empty($attribute)) {
            $filename = $this->getThumbFileName($attribute, $profile);

            return $filename ? Yii::getAlias($path . '/' . $filename) : null;
        }
        return null;
    }

    /**
     * @param string $attribute
     * @param string $profile
     * @return string|null
     */
    public function getThumbUploadUrl($attribute, $profile = 'thumb')
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        $path = $this->getUploadPath($attribute, true);
        $behavior = static::getInstance($this->owner, $attribute);
        if (!empty($path)) {
            if ($behavior->createThumbsOnRequest) {
                $behavior->createThumbs();
            }
            $url = $behavior->resolvePath($behavior->thumbUrl);
            $fileName = $model->getOldAttribute($attribute);
            $thumbName = $this->getThumbFileName($fileName, $profile);

            return Yii::$app->storage->getUrl($url . '/' . $thumbName);
        } elseif ($behavior->getPlaceholder()) {
            return $behavior->getPlaceholderUrl($profile);
        } else {
            return null;
        }
    }

    /**
     * @param $profile
     * @return string
     */
    public function getPlaceholderUrl($profile)
    {
        list ($path, $url) = Yii::$app->assetManager->publish($this->getPlaceholder());
        $filename = basename($path);
        $thumb = $this->getThumbFileName($filename, $profile);
        $thumbPath = dirname($path) . DIRECTORY_SEPARATOR . $thumb;
        $thumbUrl = dirname($url) . '/' . $thumb;

        if (!is_file($thumbPath)) {
            $this->generateImageThumb($this->thumbs[$profile], $path, $thumbPath, true);
        }

        return $thumbUrl;
    }

    /**
     * @inheritdoc
     */
    protected function delete($attribute, $old = false)
    {
        parent::delete($attribute, $old);

        $profiles = array_keys($this->thumbs);
        foreach ($profiles as $profile) {
            $path = $this->getThumbUploadPath($attribute, $profile, $old);
            if (!empty($path) && Yii::$app->storage->fileExists($path)) {
                Yii::$app->storage->delete($path);
            }
        }
    }

    /**
     * @param $filename
     * @param string $profile
     * @return string
     */
    protected function getThumbFileName($filename, $profile = 'thumb')
    {
        $info = pathinfo($filename);
        return $info['filename'] . '_' . $profile . '.' . $info['extension'];
    }

    /**
     * @param $config
     * @param $path
     * @param $thumbPath
     * @param $local
     */
    protected function generateImageThumb($config, $path, $thumbPath, $local = false)
    {
        $width = ArrayHelper::getValue($config, 'width');
        $height = ArrayHelper::getValue($config, 'height');
        $quality = ArrayHelper::getValue($config, 'quality', 100);
        $mode = ArrayHelper::getValue($config, 'mode', ManipulatorInterface::THUMBNAIL_OUTBOUND);

        if (!$width || !$height) {
            $image = Image::getImagine()->open($path);
            $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
            if ($width) {
                $height = ceil($width / $ratio);
            } else {
                $width = ceil($height * $ratio);
            }
        }

        // Fix error "PHP GD Allowed memory size exhausted".
        ini_set('memory_limit', '512M');
        if ($local) {
            Image::thumbnail($path, $width, $height, $mode)->save($thumbPath, ['quality' => $quality]);
        } else {
            $tempThumbPath = Yii::$app->getRuntimePath() . '/' . uniqid() . '.' . pathinfo($thumbPath, \PATHINFO_EXTENSION);
            Image::thumbnail($path, $width, $height, $mode)->save($tempThumbPath, ['quality' => $quality]);
            Yii::$app->storage->save($tempThumbPath, $thumbPath);
            @unlink($tempThumbPath);
        }

    }
}
