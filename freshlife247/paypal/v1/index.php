<?php
namespace Sample;
use ErrorException;

require 'config.php';

$arrConditionPaypalExpress = ["paypal_checkout", "intent", "currency", "paypal_express_checkout"];
$arrConditionPaypalNormal = ["paypal_checkout", "intent", "currency"];

if (!defined('CLIENT_ID') || !defined('CLIENT_SECRET')){
    Telegram::sendMessage("client_id and secret_key empty" . PHP_EOL . "<b>Host: </b> " . json_encode($_SERVER["HTTP_HOST"]));
    echo "<span>client_id and secret_key empty</span>";
    die;
}


if(containArray($arrConditionPaypalNormal, array_keys($_GET))) {
    require __DIR__ . '/view/paypal.php';
} else {
    throw new ErrorException();
}

function containArray($arrayTarget, $arraySearch) {
    return (count(array_intersect($arrayTarget, $arraySearch))) == count($arrayTarget) ? true : false;
}