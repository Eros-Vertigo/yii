<?php
/**
 *
 * User: yuantong
 * Date: 2023/1/31
 * Email: <yt.vertigo0927@gmail.com>
 */

namespace frontend\assets;

use yii\web\AssetBundle;

class SweetAlertAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $js = [
        'sweetalert2/dist/sweetalert2.all.js',
    ];
}