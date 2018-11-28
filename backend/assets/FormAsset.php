<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class FormAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'js/jquery-ui-1.12.1.custom/jquery-ui.min.css'
    ];
    public $js = [
        'js/jquery-ui-1.12.1.custom/jquery-ui.min.js',
        'js/input-mask/jquery.inputmask.js',
        'js/input-mask/jquery.inputmask.date.extensions.js',
        'js/input-mask/jquery.inputmask.extensions.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset'
    ];
}
