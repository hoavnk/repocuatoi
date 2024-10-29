<?php

namespace Sample;
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/helpers.php';
require dirname(__DIR__) . '/process_payment/vendor/autoload.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;


global $conn, $tableSchema;
$entityBody = file_get_contents('php://input');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    show404();
};

try {
    $data = json_decode($entityBody);

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST': refundOrder($data); break;
        
        default: show404(); die; break;
    }


} catch (\Throwable $th) {
    echo responseError($th->getMessage());
    $GLOBALS['conn']->rollback();
    die;
}                                                                                                   

function refundOrder($data) {
    if (empty($data)) {
        show404();
    }
    $keyData = selectProxyPGSetting();
    $clientId = $keyData[0]['setting_value'];
    $secretKey = $keyData[1]['setting_value'];

    $protocol = 'https://';
    $apiUrl = IS_PRODUCTION ? 'https://api.paypal.com/v1/payments/sale/' : 'https://api.sandbox.paypal.com/v1/payments/sale/';
    $apiUrl .= $data->capture_id . '/refund';


    $resAuthentication = authenticationPaypal($clientId, $secretKey);
    if (!isset($resAuthentication['access_token'])) {
        echo responseError('Error', 'Authentication fail!');
        return responseError('Error', 'Authentication fail!');
    }

    $accessToken = $resAuthentication['access_token'];

    $ch = curl_init($apiUrl);

    // Set the Authorization header with the Bearer token
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken
    ];

    // Set the request body
    $body = json_encode([
        'amount' => [
            'total' => $data->amount,
            'currency' => $data->currency
        ]
    ]);

    // Set cURL options
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

    // Execute the request
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    }

    curl_close($ch);

    // Log the response for debugging purposes
    Telegram::sendMessage($response, "Refund");

    // Set the response header to JSON
    header('Content-Type: application/json');

    // Return the response from PayPal as JSON
    echo $response;
}



function selectTable($tableName, $data) {
    $conn = $GLOBALS['conn'];
    $sql = "select TABLE $tableName (";
    foreach ($data as $key => $value) {
        $sql .= "$key  $value,";
    }
    $sql = substr_replace($sql ,"",-1);
    $sql .= ")";
    
    return $conn->query($sql) === TRUE ? true : false;
}

function selectProxyPGSetting() {
    $conn = $GLOBALS['conn'];
    $sql = "SELECT * FROM `" . PROXY_DATABASE_NAME . "`";
    $result = $conn->query($sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function authenticationPaypal($client_id, $secret_key)
{
    // Initialize cURL session
    $ch = curl_init(URL_AUTHENTICATION_PAYPAL);

    $encodedCredentials = base64_encode($client_id . ":" . $secret_key);

    // Set the Authorization header with your client ID and secret encoded in base64
    $authorization = "Authorization: Basic $encodedCredentials";

    // Set cURL options
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/x-www-form-urlencoded',
        $authorization
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
    }
    curl_close($ch);
    $responseData = json_decode($response, true);
    return $responseData;
}


function show404() {
    header("HTTP/1.0 404 Not Found");
    die;
}



