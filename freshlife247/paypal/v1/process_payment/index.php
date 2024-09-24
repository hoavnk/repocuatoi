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
        $items = [];
        foreach($data->items as $item) {
            $items[] = [
                'name' => $item->name,
                'unit_amount' => [
                    'currency_code' => $data->currency,
                    'value' => $item->total
                ],
                'quantity' => $item->quantity
            ];
        }

        $results = [
            [
                'op' => 'replace',
                'path' => "/purchase_units/@reference_id=='default'/amount",
                'value' => [
                    'currency_code' => $data->currency,
                    'value' => $data->total,
                    'breakdown' => [
                        'item_total' => ['currency_code' => $data->currency, 'value' => $data->sub_total],
                        'shipping' => ['currency_code' => $data->currency, 'value' => $data->shipping],
                        'discount' => ['currency_code' => $data->currency, 'value' => $data->discount],
                    ]
                ]
            ],
            [
                'op' => 'add',
                'path' => "/purchase_units/@reference_id=='default'/items",
                'value' => $items
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

            // If name product is over 127 characters, just get 127 characters
            if (strlen($data->items->name) > 127) {
                $data->items->name = substr($data->items->name, 0, 127);
            }

            // Add prefix platform into invoice_id
            $data->invoice_id = "TPO_" . $data->invoice_id;

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