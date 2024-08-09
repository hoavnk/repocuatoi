<?php
namespace Sample;


function responseError($msg, $data = []) {
    http_response_code(500);
    Telegram::sendMessage($msg, "ResponseError");
    return json_encode([
        "error" => true,
        "msg" => $msg,
        "data" => $data
    ]);
}


function responseSuccess($msg, $data = []) {
    Telegram::sendMessage($msg, "ResponseSuccess");
    return json_encode([
        "error" => false,
        "msg" => $msg,
        "data" => $data
    ]);
}
