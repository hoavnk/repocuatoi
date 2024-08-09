const Event = {
    listener: {
        message: function() {
            if (window.addEventListener) {
                window.addEventListener("message", handler.receiverMessage);
            } else {
                window.attachEvent("onmessage", handler.receiverMessage);
            }
        }
    },
    run: {
        resizeCreditForm: function() {

        },
        generatePaypalButton: function() {
            paypal.Buttons({
                createOrder: model.get.isExpressCheckout() ? handler.createOrderExpressCheckout  : handler.createOrder,
                onApprove: handler.onApprove,
                onInit: handler.onInit,
                onClick: handler.onClick,
                onCancel: handler.onCancel,
                onError: handler.onError,
                onShippingChange: handler.onShippingChange,
            }).render(PAYPAL_BUTTON_ELEMENT_ID);
        },
        onLoad: function() {
            handler.onLoad();
        }
    }
}

window.onload = function() {
    Event.run.resizeCreditForm();
    Event.run.generatePaypalButton();
    Event.run.onLoad();
    Event.listener.message();
}