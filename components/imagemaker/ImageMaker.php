<?php

namespace app\components\imagemaker;

use Yii;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\helpers\Url;

class ImageMaker extends Component
{
    /* @var string */
    protected $imagePath = '@webroot/screenshots';

    /* @var string */
    protected $imageUrl = '@web/screenshots';

    /* @var string */
    public $serviceUrl = '';

    public function init() {
        parent::init();

        $dir = Yii::getAlias($this->imagePath);
        if (!is_dir($dir)) {
            FileHelper::createDirectory($dir, 0777);
        }
    }

    /**
     * @param $route string|array
     * @param $name string
     * @param $type string
     * @param $fullPage boolean
     * @return  string|false
     */
    public function doScreenshot($route, $name = null, $type = 'screenshot', $fullPage = true)
    {
        if ($this->getScreenshot($name)) {
            $this->deleteScreenshot($name);
        }
        $url = rtrim(Url::to($route, true), '/');
        $screenshotUrl = $this->serviceUrl . '/?url=' . urlencode($url) . '&type=' . $type . '&fullPage=' . ($fullPage ? 'true' : 'false');

        $name = $name ?: uniqid();
        $path = Yii::getAlias($this->imagePath) . '/' . $name . '.png';
        $url = Yii::getAlias($this->imageUrl) . '/' . $name . '.png';

        try {
            if ($image = file_get_contents($screenshotUrl)) {
                if (file_put_contents($path, $image)) {
                    return $url;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $name string
     * @return boolean|string
     */
    public function getScreenshot($name)
    {
        $path = Yii::getAlias($this->imagePath) . '/' . $name . '.png';
        if (file_exists($path)) {
            return $path;
        }

        return false;
    }

    /**
     * @param $name
     * @return boolean
     */
    public function deleteScreenshot($name)
    {
        $path = Yii::getAlias($this->imagePath) . '/' . $name . '.png';
        if (file_exists($path)) {
            return unlink($path);
        }

        return true;
    }
}