<?php

namespace frontend\modules\paypal\controllers;

use frontend\modules\paypal\Paypal;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
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

    public function actionNotify(): string
    {
        $request = Yii::$app->request->post();
        if (!empty($request)) {
            Yii::warning($request);
        }
        return true;
    }
}
