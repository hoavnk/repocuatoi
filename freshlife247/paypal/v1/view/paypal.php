<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
    $disableFunding = isset($_GET['disable-funding']) ? $_GET['disable-funding'] : 'paylater,card';
    
    if (IS_PRODUCTION) {
        $paypalScript = 'https://www.paypal.com/sdk/js?client-id=' . CLIENT_ID . '&currency=USD&intent=capture&disable-funding=' . ($disableFunding);
    } else {
        $paypalScript = 'https://www.sandbox.paypal.com/sdk/js?client-id=' . CLIENT_ID . '&currency=USD&intent=capture&disable-funding=' . ($disableFunding);
    }
    ?>

    <script src="<?= $paypalScript ?>"></script>
    <link rel="stylesheet" href="/paypal/v1/styles/app.css">
</head>

<body>
    <div id="loading" class="skeleton-box"></div>
    <div id="paypal-button-container" style="display: none" class=""></div>
    <script id="script" src="/paypal/v1/scripts/build/payment-form.js"></script>
</body>

</html>