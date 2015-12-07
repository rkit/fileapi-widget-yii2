<?php

/**
 * @link https://github.com/rkit/fileapi-yii2
 * @copyright Copyright (c) 2015 Igor Romanov
 * @license [MIT](http://opensource.org/licenses/MIT)
 */

namespace rkit\fileapi;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use Yii;

/**
 * FileApi
 * Widget for https://github.com/RubaXa/jquery.fileapi/
 */
class Widget extends InputWidget
{
    /**
     * @var string FileAPI selector
     */
    public $selector;
    /**
     * @var string
     */
    public $inputName = 'file';
    /**
     * @var array {@link https://github.com/RubaXa/jquery.fileapi}
     */
    public $settings = [];
    /**
     * @var string Path to template
     */
    public $template;
    /**
     * @var array {@link https://github.com/RubaXa/jquery.fileapi/#events}
     */
    public $callbacks = [];
    /**
     * @var boolean
     */
    public $preview = true;
    /**
     * @var boolean
     */
    public $crop = false;
    /**
     * @var array {@link https://github.com/RubaXa/jquery.fileapi/#elementsobject}
     */
    private $defaultSettings = [
        'autoUpload' => true,
        'elements' => [
            'progress' => '[data-fileapi="progress"]',
            'active' => [
                'show' => '[data-fileapi="active.show"]',
                'hide' => '[data-fileapi="active.hide"]'
            ],
            'name' => '[data-fileapi="name"]',
            'preview' => [
                'el' => '[data-fileapi="preview"]',
                'width' => 200,
                'height' => 200,
                'keepAspectRatio' => true
            ],
            'dnd' => [
                'el' => '.fileapi-dnd',
                'hover' => '.fileapi-dnd-active'
            ]
        ]
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $request = Yii::$app->getRequest();

        if ($request->enableCsrfValidation === true) {
            $this->settings['data'][$request->csrfParam] = $request->getCsrfToken();
        }

        if (!isset($this->settings['url'])) {
            $this->settings['url'] = $request->getUrl();
        }

        if ($this->crop === true) {
            $this->settings['autoUpload'] = false;
        }

        $this->settings = ArrayHelper::merge($this->defaultSettings, $this->settings);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerScripts();

        if ($this->hasModel()) {
            $input = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $input = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        return $this->render(
            $this->template,
            [
                'selector' => $this->getSelector(),
                'input' => $input,
                'inputName' => $this->inputName,
                'value' => $this->hasModel() ? $this->model->{$this->attribute} : $this->value,
                'preview' => $this->preview,
                'crop' => $this->crop,
                'model' => $this->model,
                'attribute' => $this->attribute
            ]
        );
    }

    /**
     * @return string
     */
    public function getSelector()
    {
        return $this->selector !== null ? $this->selector : 'fileapi-' . $this->options['id'];
    }

    /**
     * Register scripts
     */
    public function registerScripts()
    {
        $view = $this->getView();

        Asset::register($view);
        if ($this->crop === true) {
            CropAsset::register($view);
        }

        $view->registerJs(
            'jQuery(\'#' . $this->getSelector() . '\').fileapi(' . Json::encode($this->settings) .');'
        );

        $this->registerCallbacks();
    }

    /**
     * Register widget callbacks
     */
    protected function registerCallbacks()
    {
        if (!empty($this->callbacks)) {
            $selector = $this->getSelector();
            $view = $this->getView();
            foreach ($this->callbacks as $event => $callback) {
                if (is_array($callback)) {
                    foreach ($callback as $function) {
                        if (!$function instanceof JsExpression) {
                            $function = new JsExpression($function);
                        }
                        $view->registerJs("jQuery('#$selector').on('$event', $function);");
                    }
                } else {
                    if (!$callback instanceof JsExpression) {
                        $callback = new JsExpression($callback);
                    }
                    $view->registerJs("jQuery('#$selector').on('$event', $callback);");
                }
            }
        }
    }
}
