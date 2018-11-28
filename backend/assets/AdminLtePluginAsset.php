<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AdminLtePluginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte';
    public $js = [
        'bower_components/datatables.net/js/jquery.dataTables.min.js',
        'bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
        // more plugin Js here
    ];
    public $css = [
        'bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
        // more plugin CSS here
    ];
    public $depends = [
        'dmstr\web\AdminLteAsset',
    ];
}
