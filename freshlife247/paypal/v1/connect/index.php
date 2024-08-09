<?php

namespace Sample;
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/helpers.php';
require dirname(__DIR__) . '/process_payment/vendor/autoload.php';

use PayPalCheckoutSdk\Core\AccessTokenRequest;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Webhooks\WebhookConnectRequest;


global $conn, $tableSchema;
$entityBody = file_get_contents('php://input');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    show404();
};

try {
    $data = json_decode($entityBody);

    if (!generateTableWhenNotExist()) {
        echo responseError("There was an error creating the table data");
        die;
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST': connectClientId($data); break;
        
        default: show404(); die; break;
    }


} catch (\Throwable $th) {
    echo responseError($th->getMessage());
    $GLOBALS['conn']->rollback();
    die;
}

function connectClientId($data) {
    if (empty($data)) {
        show404();
    }
    deleteAllRow();
    $input = [
        "setting_key" => array_keys((array)$data),
        "setting_value" => array_values((array)$data)
    ];
    $insert = insertRow($input);
    $webhook = setWebhook($data->client_id, $data->secret_key);
    if ($insert && $webhook) {
        $GLOBALS['conn']->commit();
        $hashInput = array_merge(array_values((array)$data), [PRIVATE_KEY]);
        echo responseSuccess("Success", hash256($hashInput));
    }else if (!$webhook){
        echo responseError("Can't setup webhook!");
        $GLOBALS['conn']->rollback();
    }else{
        echo responseError("Error insert database");
        $GLOBALS['conn']->rollback();
    }
}


function generateTableWhenNotExist() {
    if (!isExistTable(PROXY_DATABASE_NAME)) {
        return createTable(PROXY_DATABASE_NAME, $GLOBALS['tableSchema']);
    }else {
        return true;
    }
}

function isExistTable($table) {
    $conn = $GLOBALS['conn'];
    if ($result = $conn->query("SHOW TABLES LIKE '" . $table . "'")) {
        if($result->num_rows == 1) {
            return true;
        }else{
            return false;
        }
    }
}

function createTable($tableName, $data) {
    $conn = $GLOBALS['conn'];
    $sql = "CREATE TABLE $tableName (";
    foreach ($data as $key => $value) {
        $sql .= "$key  $value,";
    }
    $sql = substr_replace($sql ,"",-1);
    $sql .= ")";
    
    return $conn->query($sql) === TRUE ? true : false;
}

function insertRow($data) {
    $conn = $GLOBALS['conn'];
    $sql = "INSERT INTO `" . PROXY_DATABASE_NAME . "` (";

    foreach ($data as $key => $value) {
        $sql .= "`$key`,";
    }
    $sql = substr_replace($sql ,"",-1) . ")" . PHP_EOL;
    $sql .= " VALUES ";
    foreach ($data["setting_key"] as $key => $value) {
        $sql .= "('" . $value . "','" . $data["setting_value"][$key] . "'),";
    }
    $sql = substr_replace($sql ,"",-2) . ")";

    return $conn->query($sql) === TRUE ? true : false;

}

function deleteAllRow() {
    $conn = $GLOBALS['conn'];
    $sql = "DELETE FROM `" . PROXY_DATABASE_NAME . "`";
    return $conn->query($sql) === TRUE ? true : false;
}

function hash256($values) {
    return hash('sha256', join("", $values));
}

function show404() {
    header("HTTP/1.0 404 Not Found");
    die;
}

function setWebhook($clientId, $secretKey) {
        $protocol = 'https://';
        if (IS_PRODUCTION) {
            $environment = new ProductionEnvironment($clientId, $secretKey);
        }else{
            $environment = new SandboxEnvironment($clientId, $secretKey);
        }
        $client = new PayPalHttpClient($environment);
        $request = new WebhookConnectRequest();
        $request->body =  [
            'url' => $protocol . $_SERVER['HTTP_HOST'] . WEBHOOK_URI,
            'event_types' => [
                [
                    'name' => "CHECKOUT.ORDER.APPROVED"
                ],
                [
                    'name' => "PAYMENT.CAPTURE.COMPLETED"
                ],
                [
                    'name' => "PAYMENT.CAPTURE.REFUNDED"
                ],
                [
                    'name' => "CUSTOMER.DISPUTE.CREATED"
                ],
                [
                    'name' => "CUSTOMER.DISPUTE.RESOLVED"
                ]
            ]
        ];

        Telegram::sendMessage(json_encode($request->body), "Connect Webhook");

        $response = $client->execute($request);
        return $response->statusCode == 201 ? true : false;
}
