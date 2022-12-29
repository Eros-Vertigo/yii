<?php
/**
 * Paypal Payments 贝宝支付网关
 * User: yuantong
 * Date: 2022/12/28
 * Email: <yt.vertigo0927@gmail.com>
 */

namespace frontend\modules\paypal;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Yii;
use yii\base\Model;

class Paypal extends Model
{
    const APPROVE = 'approve';
    const PAYPAL_ACCESS_TOKEN = 'key:paypal:access_token';
    const STATUS = [
        'CREATED' => 201
    ];

    /**
     * @var string client_id
     * @link https://developer.paypal.com/dashboard/applications/edit/
     */
    public string $clientId;
    /**
     * @var string client_secret
     */
    public string $clientSecret;

    public array $links;
    /**
     * @var mixed 同步回调地址
     */
    private $return_url;
    /**
     * @var mixed 取消支付地址
     */
    private $cancel_url;
    /**
     * @var mixed 货币代码
     */
    public $currency_code = 'USD';
    /**
     * @var mixed 金额
     */
    public $amount = '100.00';
    /**
     * @var mixed 产品名称
     */
    public $product_name = 'shenlan test';
    /**
     * @var mixed Paypal Order ID
     */
    public $order_id;

    /**
     * 初始化一些参数
     */
    public function init()
    {
        $this->clientId = "AQCSuHaPI93q833esvuYkXrKzhYR-fgXcZJu5FUh6yTMMIT20Lk4Q4AoFPw7DifHCZ8Vty-qoJToNvBm";
        $this->clientSecret = "ECac-JxHHlJ-hDH9CBF5XH3qYMCGPcaa2He8IfGNM1opI87Vx00GkCYIa9iJgujpwVa3nvxuTNjjSyV0";
        $this->return_url = '';
        $this->cancel_url = '';
    }

    /**
     * 生成 access_token
     * @throws GuzzleException
     */
    public function generateToken()
    {
        $redis = Yii::$app->redis;
        if ($redis->get(self::PAYPAL_ACCESS_TOKEN)) {
            return $redis->get(self::PAYPAL_ACCESS_TOKEN);
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
        $redis->set(self::PAYPAL_ACCESS_TOKEN, $result['access_token']);
        $redis->expire(self::PAYPAL_ACCESS_TOKEN, $result['expires_in']);

        return $result['access_token'];
    }

    /**
     * 创建订单
     * @throws GuzzleException
     * if create order successful, return order_id and approve link. then redirect url to pay
     */
    public function checkoutOrder()
    {
        $client = new Client([
            'base_uri' => 'https://api-m.sandbox.paypal.com',
            'timeout' => 5.0
        ]);
        try {
            $res = $client->post('/v2/checkout/orders', [
                'headers' => ['Authorization' => 'Bearer ' . $this->generateToken()],
                'json' => [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [
                        [
                            'name' => $this->product_name,
                            'amount' => [
                                'currency_code' => $this->currency_code,
                                'value' => $this->amount,
                            ]
                        ]
                    ],
                    'application_context' => [
                        'return_url' => $this->return_url,
                        'cancel_url' => $this->cancel_url,
                    ]
                ]
            ]);
            if ($res->getStatusCode() != self::STATUS['CREATED']) {
                return false;
            }
            $result = json_decode($res->getBody()->getContents(), true);
            $this->order_id = $result['id'];
            $this->links = $result['links'];
            return $this->getHref(self::APPROVE);
        } catch (ClientException $e) {
            return $e->getMessage();
        }
    }

    /**
     * 根据 rel 从 links 获取 href
     * @param $rel mixed restful response link collection
     * @return mixed|null
     */
    public function getHref($rel)
    {
        if (is_array($this->links)) {
            foreach ($this->links as $link) {
                if ($link['rel'] == $rel) {
                    return $link['href'];
                }
            }
        }
        return null;
    }

}