<?php

namespace Sample;

require_once dirname(__DIR__) . '/config.php';


try {
  $entityBody = file_get_contents('php://input');
  try {
    $message = $entityBody == "" ? "<b>Lỗi</b> Webhook Empty content" : $entityBody;
    Telegram::sendMessage($message);
  } catch (\Throwable $th) { }
  //The url you wish to send the POST request to
  $url = HOME_BASE_URI . PAYPAL_WEBHOOK;

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $entityBody);
  $result = curl_exec($ch);
  echo $result;
} catch (\Exception $th) {
  Telegram::report($th);
}