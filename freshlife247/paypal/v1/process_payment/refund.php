<?php

namespace Sample;

require __DIR__ . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class RefundOrder
{
    private static function buildRequestBody($data)
    {
        $body = [
            'amount' => [
                'value' => $data->amount,
                'currency_code' => $data->currency
            ]
        ];

        return $body;
    }

    public static function run()
    {
        try {
            $body = file_get_contents('php://input');
            $data = json_decode($body);
            $captureId = $data->capture_id;
           
            if (IS_PRODUCTION) {
                $environment = new ProductionEnvironment(CLIENT_ID, CLIENT_SECRET);
            }else{
                $environment = new SandboxEnvironment(CLIENT_ID, CLIENT_SECRET);
            }
            $client = new PayPalHttpClient($environment);
            $request = new CapturesRefundRequest($captureId);
            $request->prefer("return=representation");
            $request->body = RefundOrder::buildRequestBody($data);

            $response = $client->execute($request);

            echo json_encode($response);
            Telegram::sendMessage(json_encode($response), "Refund");
            return true;
        } catch (\Exception $th) {
            http_response_code(500);
            echo json_encode($th);
            Telegram::report($th, $body);
        }
    }
}

RefundOrder::run();