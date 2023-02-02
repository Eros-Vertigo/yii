<?php
/**
 *
 * User: yuantong
 * Date: 2023/1/31
 * Email: <yt.vertigo0927@gmail.com>
 */

namespace frontend\controllers;

use yii\web\Controller;

class AlertController extends Controller
{
    public function actionIndex(): string
    {
        return $this->render('index');
    }
}