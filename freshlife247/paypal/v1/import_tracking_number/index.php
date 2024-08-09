<?php

namespace Sample;
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/database.php';
require_once dirname(__DIR__) . '/helpers.php';
require dirname(__DIR__) . '/process_payment/vendor/autoload.php';

global $conn, $tableSchema;

$entityBody = file_get_contents('php://input');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    show404();
};
try {
    $data = json_decode($entityBody);
    $arrData = cvf_convert_object_to_array($data);
    $privateKey = $arrData['private_key'] ?? null;

    $keyData = selectProxyPGSetting();
    $client_id = $keyData[0]['setting_value'];
    $secret_key = $keyData[1]['setting_value'];
    $hashInput = [
        $client_id,
        $secret_key,
        PRIVATE_KEY
    ];
    $hashCode = hash256($hashInput);
    if ($privateKey === $hashCode) {
        addTrackingNumber($arrData, $client_id, $secret_key);
    } else {
        echo responseError("Unauthorized");
        die;
    }
 } catch (\Throwable $th) {
     echo $th->getMessage();
     die;
 }


function show404() {
    header("HTTP/1.0 404 Not Found");
    echo 'Method not allowed';
    die;
}

function hash256($values) {
    return hash('sha256', join("", $values));
}

function addTrackingNumber($parameters, $client_id, $secret_key) {
    $resAuthentication = authenticationPaypal($client_id, $secret_key);
    if (!isset($resAuthentication['access_token'])) {
        echo responseError('Error', 'Authentication fail!');
        return responseError('Error', 'Authentication fail!');
    }

    $body = json_encode(
        array(
            'trackers' => $parameters["trackers"]
        )
    );

    $response = wp_remote_post(URL_IMPORT_TKN_PAYPAL, array(
        'body' => $body,
        'headers' => array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $resAuthentication['access_token']
        )
    ));
    
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    http_response_code($response_code);
    if ($response_code !== 200) {
        echo responseError('Error', json_decode($response_body));
        return responseError('Error', json_decode($response_body));
    }
    echo responseSuccess('Tracking number imported successfully', $response_body);
    return responseSuccess('Tracking number imported successfully', $response_body);
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

function selectProxyPGSetting() {
    $conn = $GLOBALS['conn'];
    $sql = "SELECT * FROM `" . PROXY_DATABASE_NAME . "`";
    $result = $conn->query($sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    return $data;
}

function cvf_convert_object_to_array($data) {
    if (is_object($data)) {
        $data = get_object_vars($data);
    }
    if (is_array($data)) {
        return array_map(__FUNCTION__, $data);
    } else {
        return $data;
    }
}
