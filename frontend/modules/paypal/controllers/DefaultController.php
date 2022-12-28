<?php

namespace frontend\modules\paypal\controllers;

use frontend\modules\paypal\Paypal;
use yii\web\Controller;

/**
 * Default controller for the `PayPal` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex(): string
    {
        $paypal = new Paypal();
        var_dump($paypal->checkoutOrder());
        return $this->render('index');
    }
}
