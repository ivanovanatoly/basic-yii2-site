<?php

namespace app\widgets;

use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Nav renders a nav HTML component.
 *
 * For example:
 *
 * ```php
 * echo Nav::widget([
 *     'items' => [
 *         [
 *             'label' => 'Home',
 *             'url' => ['site/index'],
 *             'linkOptions' => [...],
 *         ],
 *         [
 *             'label' => 'Dropdown',
 *             'items' => [
 *                  ['label' => 'Level 1 - Dropdown A', 'url' => '#'],
 *                  '<li class="divider"></li>',
 *                  '<li class="dropdown-header">Dropdown Header</li>',
 *                  ['label' => 'Level 1 - Dropdown B', 'url' => '#'],
 *             ],
 *         ],
 *         [
 *             'label' => 'Login',
 *             'url' => ['site/login'],
 *             'visible' => Yii::$app->user->isGuest
 *         ],
 *     ],
 *     'options' => ['class' =>'etc'],
 * ]);
 * ```
 */
class Nav extends Widget
{
    public $items = [];

    public $encodeLabels = true;

    public $activateItems = true;

    public $activateParents = false;

    public $route;

    public $params;

    public function init()
    {
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        Html::addCssClass($this->options, ['widget' => 'list-group']);
        $this->options['id'] = 'mainnav-menu';
    }

    public function run()
    {
        BootstrapAsset::register($this->getView());
        return $this->renderItems();
    }

    public function renderItems()
    {
        $items = [];
        foreach ($this->items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

    protected function renderSubItems($items, $options = [])
    {
        $lines = [];
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                continue;
            }
            $lines[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $lines), $options);
    }

    public function renderItem($item)
    {
        if (is_string($item)) {
            return $item;
        }
        if (!isset($item['label'])) {
            throw new InvalidConfigException("The 'label' option is required.");
        }
        $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
        $label = $encodeLabel ? Html::encode($item['label']) : $item['label'];
        $options = ArrayHelper::getValue($item, 'options', []);
        $items = ArrayHelper::getValue($item, 'items');
        $url = ArrayHelper::getValue($item, 'url', '#');
        $linkOptions = ArrayHelper::getValue($item, 'linkOptions', []);

        if (isset($item['active'])) {
            $active = ArrayHelper::remove($item, 'active', false);
        } else {
            $active = $this->isItemActive($item);
        }

        $childActive = false;

        if (empty($items)) {
            $items = '';
        } else {
            if (is_array($items)) {
                if ($this->activateItems) {
                    $items = $this->isChildActive($items, $childActive);
                }
                $items = $this->renderSubItems($items, ArrayHelper::getValue($item, 'dropDownOptions', []));
            }
        }

        if ($this->activateItems) {
            if ($childActive || $active) {
                Html::addCssClass($options, 'active');
            }
            if ($active && !$childActive || ($this->activateParents && $childActive)) {
                Html::addCssClass($options, 'active-link');
            }
        }

        $label = Html::tag('span', $label, ['class' => 'menu-title']);
        if (isset($item['icon'])) {
            $label = Html::tag('i', '', ['class' => $item['icon']]) . $label;
        }

        return Html::tag('li', Html::a($label, $url, $linkOptions) . $items, $options);
    }

    protected function isChildActive($items, &$active)
    {
        foreach ($items as $i => $child) {
            if (ArrayHelper::remove($items[$i], 'active', false) || $this->isItemActive($child)) {
                Html::addCssClass($items[$i]['options'], 'active active-link');
                $active = true;
            }
        }
        return $items;
    }

    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }
            if (ltrim($route, '/') !== $this->route) {
                return false;
            }
            unset($item['url']['#']);
            if (count($item['url']) > 1) {
                $params = $item['url'];
                unset($params[0]);
                foreach ($params as $name => $value) {
                    if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
