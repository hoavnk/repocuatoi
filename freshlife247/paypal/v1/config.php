<?php
require 'telegram.php';
require 'database.php';

define("PROXY_DATABASE_NAME", "proxy_paygate_setting");
define("HOME_BASE_URI", IS_PRODUCTION ? "https://apigw.api.theplus1.net" : "https://apigw.devzone.theplus1.net");
define("PAYPAL_WEBHOOK", "/webhooks/proxy-paypal");
define("PAYPAL_DISPUTE_WEBHOOK", "/webhooks/proxy-paypal");
define("PRIVATE_KEY", "9340e4649eab93dfa36a4e0bb585a39a04fd9beedf69f7973cd3356b48cad7e6");

define("WEBHOOK_URI", "/paypal/v1/webhook/");
define(
    "URL_AUTHENTICATION_PAYPAL",
     IS_PRODUCTION ? "https://api.paypal.com/v1/oauth2/token"
      : "https://api-m.sandbox.paypal.com/v1/oauth2/token"
);

define(
    "URL_IMPORT_TKN_PAYPAL",
     IS_PRODUCTION ? "https://api.paypal.com/v1/shipping/trackers-batch"
      : "https://api-m.sandbox.paypal.com/v1/shipping/trackers-batch"
);

$clientId = null;
$secretKey = null;
$query = $conn->query("SELECT * FROM " . PROXY_DATABASE_NAME  . " WHERE setting_key='client_id' OR setting_key='secret_key'");
$results = [];
if ($query && mysqli_num_rows($query) > 0) {
    while($row = mysqli_fetch_assoc($query)) {
        $results[] = $row;
    }
}

foreach ($results as $key => $value) {
    if ($value["setting_key"] == "client_id") {   
        define("CLIENT_ID", $value["setting_value"]);
    }
    if ($value["setting_key"] == "secret_key") {
        define("CLIENT_SECRET", $value["setting_value"]);
    }
}

