const handler = {
    createOrderExpressCheckout: async function (_, actions) {
        return await handler.wait(['cart.orderSession.sessionId']).then(async function(formInfo) {
            const { totalPrice } = formInfo;
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        currency_code: CURRENCY_CODE,
                        value: totalPrice.toFixed(2)
                    }
                }],
                application_context: APPLICATION_CONTEXT
            });
        })
    },
    createOrder: function (_, actions) {
        const formInfo = model.get.formInfo();
        const { shippingInfo } = formInfo;
        const { totalPrice } = formInfo;

        const payerData = {
            email_address: shippingInfo[FORM_INFO_INTERFACE.email],
            name: {
                surname: shippingInfo[FORM_INFO_INTERFACE.lastName],
                given_name: shippingInfo[FORM_INFO_INTERFACE.firstName],
            },
        }

        const orderIntent = "CAPTURE";
        const applicationContext = {...APPLICATION_CONTEXT};
        const purchaseUnits = [];

        if (
            helper.isset(shippingInfo[FORM_INFO_INTERFACE.country]) &&
            helper.isset(shippingInfo[FORM_INFO_INTERFACE.city])
        ) {
            payerData.address = {
                country_code: shippingInfo[FORM_INFO_INTERFACE.countryCode],
                address_line1: shippingInfo[FORM_INFO_INTERFACE.address],
                address_line2: shippingInfo[FORM_INFO_INTERFACE.apartment],
                admin_area_1: shippingInfo[FORM_INFO_INTERFACE.province],
                admin_area_2: shippingInfo[FORM_INFO_INTERFACE.city],
                postal_code: shippingInfo[FORM_INFO_INTERFACE.zipCode],
            }
        } else {
            applicationContext.shipping_preference = "NO_SHIPPING";
        }

        if (helper.isset(shippingInfo[FORM_INFO_INTERFACE.phoneNumber])) {
            payerData.phone = {
                phone_type: "HOME",
                phone_number: {
                    national_number: shippingInfo[FORM_INFO_INTERFACE.phoneNumber].replace(/[^0-9]+/g, '')
                }
            }
        }

        purchaseUnits.push({
            amount: {
                currency_code: CURRENCY_CODE,
                value: totalPrice.toFixed(2)
            },
            shipping: {
                address: {
                    country_code: shippingInfo[FORM_INFO_INTERFACE.countryCode],
                    address_line1: shippingInfo[FORM_INFO_INTERFACE.address],
                    address_line2: shippingInfo[FORM_INFO_INTERFACE.apartment],
                    admin_area_1: shippingInfo[FORM_INFO_INTERFACE.province],
                    admin_area_2: shippingInfo[FORM_INFO_INTERFACE.city],
                    postal_code: shippingInfo[FORM_INFO_INTERFACE.zipCode],
                },
                name: {
                    full_name: `${shippingInfo[FORM_INFO_INTERFACE.firstName]} ${shippingInfo[FORM_INFO_INTERFACE.lastName]}`
                }
            }
        });

        const order = actions.order.create({
            intent: orderIntent,
            purchase_units: purchaseUnits,
            payer: payerData,
            application_context: applicationContext
        });

        return order
    },
    onApprove: function (data) {
        helper.postMessage(POST_MESSAGE_PARAMS.paypalApprovedOrder, {...data, isExpressCheckout: model.get.isExpressCheckout()});
        // view.showButton();
    },
    onInit: function (_, actions) {
        view.hideLoading();
        view.showButton();
        if (model.get.isExpressCheckout()) return;

        actions.disable();
        model.set.actions(actions);
        setInterval(() => {
            helper.postMessage(POST_MESSAGE_PARAMS.isValidForm, "*");
        }, 150);
    },
    onClick: function (data, actions) {
        const { fundingSource } = data;

        // express checkout not valid form
        if (!model.get.isValidForm() && !model.get.isExpressCheckout()) {
            helper.postMessage(POST_MESSAGE_PARAMS.checkForm, "*");
            return actions.reject();
        }
        if (fundingSource == "paypal") {
            view.hideButton();
            helper.postMessage(POST_MESSAGE_PARAMS.paypalOpenPopup, "*");
            return
        }

        if (fundingSource == "card") {
            helper.postMessage(POST_MESSAGE_PARAMS.paypalOpenCreditForm, document.querySelector(PAYPAL_BUTTON_ELEMENT_ID).scrollHeight);
            return
        }
    },
    onCancel: function () {
        view.showButton();
        helper.postMessage(POST_MESSAGE_PARAMS.paypalClosePopup, "*");
    },
    onError: function () {
        view.showButton();
        helper.postMessage(POST_MESSAGE_PARAMS.paypalClosePopup, "*");
    },
    onShippingChange: function (data, actions) {
        helper.postMessage(POST_MESSAGE_PARAMS.paypalShippingChange, data);
    },
    onLoad: function () {
        helper.postMessage(POST_MESSAGE_PARAMS.loaded, "*");
        view.hideButton();
    },
    receiverMessage: function (event) {
        if (
            (typeof event.data === 'object') &&
            event.data.name === POST_MESSAGE_PARAMS.sendOrderInfo
        ) {
            model.set.formInfo(event.data.params);
        }
        if (
            (typeof event.data === 'object') &&
            event.data.name === POST_MESSAGE_PARAMS.isValidForm &&
            !model.get.isExpressCheckout()
        ) {
            model.set.isValidForm(event.data.params);
            event.data.params ? model.get.actions().enable() : model.get.actions().disable();
        }
    },
    wait: async function(values) {
        return new Promise((resolve) => {
            const interval = setInterval(function() {
                const formInfo = model.get.formInfo();
                if (values.every(val => !!helper.getValue(formInfo, val))) {
                    resolve(formInfo);
                    clearInterval(interval);
                }
            }, 100);
        });
    }
}