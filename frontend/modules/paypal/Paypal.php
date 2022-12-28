<?php
/**
 *
 * User: yuantong
 * Date: 2022/12/28
 * Email: <yt.vertigo0927@gmail.com>
 */

namespace frontend\modules\paypal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Middleware;
use Yii;
use yii\base\Model;

class Paypal extends Model
{
    public string $clientId;
    public string $clientSecret;

    public function init()
    {
        $this->clientId = "AQCSuHaPI93q833esvuYkXrKzhYR-fgXcZJu5FUh6yTMMIT20Lk4Q4AoFPw7DifHCZ8Vty-qoJToNvBm";
        $this->clientSecret = "ECac-JxHHlJ-hDH9CBF5XH3qYMCGPcaa2He8IfGNM1opI87Vx00GkCYIa9iJgujpwVa3nvxuTNjjSyV0";
    }

    /**
     * 生成 access_token
     * @throws GuzzleException
     */
    public function generateToken()
    {
        $redis = Yii::$app->redis;
        if ($redis->get('paypal-token')) {
            return $redis->get('paypal-token');
        }

        $client = new Client([
            'base_uri' => 'https://api-m.sandbox.paypal.com'
        ]);
        $res = $client->post('/v1/oauth2/token', [
            'auth' => [
                $this->clientId, $this->clientSecret
            ],
            'form_params' => [
                'grant_type' => 'client_credentials'
            ]
        ]);

        if ($res->getStatusCode() != 200) {
            return false;
        }
        $result = json_decode($res->getBody()->getContents(), true);
        // 缓存token
        $redis->set('paypal-token', $result['access_token']);
        $redis->expire('paypal-token', $result['expires_in']);

        return $result['access_token'];
    }

    /**
     * 创建订单
     * @throws GuzzleException
     */
    public function checkoutOrder()
    {
        $client = new Client([
            'base_uri' => 'https://api-m.sandbox.paypal.com',
            'timeout' => 2.0
        ]);
        try {
            $res = $client->post('/v2/checkout/orders', [
                'headers' => ['Authorization' => 'Bearer ' . $this->generateToken()],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'items' => [
                                [
                                    'name' => 'T-Shirt',
                                    'description' => 'Red XL',
                                    'quantity' => 1,
                                    'unit_amount' => [
                                        'currency_code' => 'USD',
                                        'value' => '100.00',
                                    ]
                                ]
                            ],
                            'amount' => [
                                'currency_code' => 'USD',
                                'value' => '100.00',
                                'breakdown' => [
                                    'item_total' => [
                                        'currency_code' => 'USD',
                                        'value' => '100.00'
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'application_context' => [
                        'return_url' => 'https://example.com/return',
                        'cancel_url' => 'https://example.com/cancel',
                    ]
                ]
            ]);
            if ($res->getStatusCode() != 201) {
                return false;
            }
            $result = json_decode($res->getBody()->getContents(), true);
            echo "<pre>";
            var_dump($result);
            echo "<pre>";
            exit;
        } catch (ClientException $e) {
            echo "<pre>";
            var_dump($e->getMessage());
            echo "<pre>";
            exit;
        }
    }
}