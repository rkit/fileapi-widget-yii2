<?php

namespace rkit\fileapi;

use yii\web\AssetBundle;

class CropAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@vendor/rubaxa/fileapi';
    /**
     * @inheritdoc
     */
    public $css = [
        'jcrop/jquery.Jcrop.min.css'
    ];
    /**
     * @inheritdoc
     */
    public $js = [
        'jcrop/jquery.Jcrop.min.js'
    ];
    /**
     * @inheritdoc
     */
    public $depends = [
        'rkit\fileapi\Asset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
