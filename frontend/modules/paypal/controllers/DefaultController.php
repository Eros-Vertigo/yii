<?php

namespace frontend\modules\paypal\controllers;

use frontend\modules\paypal\Paypal;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\helpers\FileHelper;
use yii\web\Controller;
use yii\web\Response;

/**
 * Default controller for the `PayPal` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return Response|string
     * @throws GuzzleException
     */
    public function actionIndex(): Response
    {
        $paypal = new Paypal();
        $url = $paypal->checkoutOrder();
        if ($url) {
            return $this->redirect($url);
        }
        return $this->render('index');
    }

    public function actionNotify()
    {
        $post = Yii::$app->request->getRawBody();
        $post = json_decode($post, true);
        self::logResult($post['resource']['purchase_units']);
    }

    public function actionSync()
    {
        $get = Yii::$app->request->get();
        $post = Yii::$app->request->post();
        echo "<pre>";
        var_dump($get, $post);
        echo "<pre>";
        exit;
    }

    public static function logResult($content, $prefix = 'test', $date = true)
    {
        $logFilePath = Yii::$aliases['@runtime'] . DIRECTORY_SEPARATOR . $prefix . DIRECTORY_SEPARATOR;
        $logFileName = date('Y-m-d', time()) . '.log';

        //创建目录
        if (!is_dir($logFilePath)) {
            FileHelper::createDirectory($logFilePath);
        }

        //将数组转成json数据，写入文件
        if (is_array($content)) {
            $content = json_encode($content);
        }

        //写数据到文件
        @file_put_contents($logFilePath . $logFileName, sprintf('执行日期:%s -- %s', date('Y-m-d H:i:s', time()), $content) . PHP_EOL, FILE_APPEND);
    }
}
