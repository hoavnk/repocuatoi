<?php

namespace PayPalCheckoutSdk\Webhooks;

use PayPalHttp\HttpRequest;

class WebhookConnectRequest extends HttpRequest
{
    function __construct()
    {
        parent::__construct("/v1/notifications/webhooks?", "POST");

        $this->headers["Content-Type"] = "application/json";
    }
}
