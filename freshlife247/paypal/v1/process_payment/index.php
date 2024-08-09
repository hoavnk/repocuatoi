<?php
namespace Sample;
use PayPalHttp\HttpException;

require __DIR__ . '/vendor/autoload.php';
require_once dirname(__DIR__) . '/config.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersPatchRequest;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PatchOrder
{
    private static function buildRequestBody($data)
    {
        $items = [
            [
                'name' => $data->items->name,
                'unit_amount' => [
                    'currency_code' => $data->currency,
                    'value' => $data->total
                ],
                'quantity' => $data->items->quantity
            ]
        ];

        $results = [
            [
                'op' => 'replace',
                'path' => "/purchase_units/@reference_id=='default'",
                'value' => [
                    'amount' => [
                        'currency_code' => $data->currency,
                        'value' => $data->total,
                        'breakdown' => [
                            'item_total' => ['currency_code' => $data->currency, 'value' => $data->total]
                        ]
                    ],
                    'items' => $items
                ]
            ],
            [
                'op' => 'add',
                'path' => "/purchase_units/@reference_id=='default'/invoice_id",
                'value' => $data->invoice_id
            ]
        ];

        return $results;
    }

    public static function patchOrder()
    {
        try {
            $body = file_get_contents('php://input');
            $data = json_decode($body);
            $orderId = $data->pp_order_id;
            if (IS_PRODUCTION) {
                $environment = new ProductionEnvironment(CLIENT_ID, CLIENT_SECRET);
            }else{
                $environment = new SandboxEnvironment(CLIENT_ID, CLIENT_SECRET);
            }
            $client = new PayPalHttpClient($environment);
            $request = new OrdersPatchRequest($orderId);
            $request->body = PatchOrder::buildRequestBody($data);
            $response = $client->execute($request);

            $response = $client->execute(new OrdersGetRequest($orderId));

            $request = new OrdersCaptureRequest($orderId);
            $response = $client->execute($request);

            echo json_encode($response);
            return $response;
        }catch (HttpException $e) {
            http_response_code(500);
            error_log($e->getMessage());
            echo $e->getMessage();
            return $e;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode($e);
            // Telegram::report($e, $body);
            return $e;
        }
    }
}

PatchOrder::patchOrder();