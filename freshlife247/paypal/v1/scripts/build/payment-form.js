const POST_MESSAGE_PARAMS = {
        sendOrderInfo: 'paypalSendOrderInfo',
        paypalApprovedOrder: 'paypalApprovedOrder',
        paypalCloseCreditForm: 'paypalCloseCreditForm',
        paypalOpenCreditForm: 'paypalOpenCreditForm',
        paypalOpenPopup: 'paypalOpenPopup',
        paypalClosePopup: 'paypalClosePopup',
        isValidForm: 'isValidForm',
        checkForm: 'checkForm',
        loaded: 'loaded',
        paypalShippingChange: 'paypalShippingChange',
    },
    APPLICATION_CONTEXT = { brand_name: 'merchant', user_action: 'CONTINUE' },
    CURRENCY_CODE = 'USD',
    PAYPAL_BUTTON_ELEMENT_ID = '#paypal-button-container',
    LOADING_ELEMENT_ID = '#loading',
    PAYPAL_EXPRESS_CHECKOUT = 'paypal_express_checkout',
    FORM_INFO_INTERFACE = {
        email: 'email',
        address: 'addressDetail',
        apartment: 'apartment',
        city: 'city',
        country: 'country',
        countryCode: 'countryCode',
        firstName: 'firstName',
        lastName: 'lastName',
        phoneNumber: 'phoneNumber',
        province: 'province',
        zipCode: 'zipCode',
    };
const helper = {
    postMessage: function (e, r, n = '*') {
        parent.postMessage({ name: e, params: r, id: helper.getParameter('id') }, n);
    },
    isset: function (e) {
        return !!((Array.isArray(e) && 0 < e.length) || e);
    },
    getParameter: function (e, r = window.location.href) {
        e = e.replace(/[\[\]]/g, '\\$&');
        e = new RegExp('[?&]' + e + '(=([^&#]*)|&|#|$)').exec(r);
        return e ? (e[2] ? decodeURIComponent(e[2].replace(/\+/g, ' ')) : '') : null;
    },
    formatCurrency: function (e) {
        return e?.toLocaleString('en-US', { style: 'currency', currency: 'USD' }) ?? '0';
    },
    getValue: function (e, r) {
        return [e].concat(r?.split('.'))?.reduce(function (e, r) {
            return e?.[r];
        });
    },
};
const Event = {
    listener: {
        message: function () {
            window.addEventListener ? window.addEventListener('message', handler.receiverMessage) : window.attachEvent('onmessage', handler.receiverMessage);
        },
    },
    run: {
        resizeCreditForm: function () {
        },
        generatePaypalButton: function () {
            paypal
                .Buttons({
                    createOrder: model.get.isExpressCheckout() ? handler.createOrderExpressCheckout : handler.createOrder,
                    onApprove: handler.onApprove,
                    onInit: handler.onInit,
                    onClick: handler.onClick,
                    onCancel: handler.onCancel,
                    onError: handler.onError,
                    // onShippingChange: handler.onShippingChange,
                })
                .render(PAYPAL_BUTTON_ELEMENT_ID);
        },
        onLoad: function () {
            handler.onLoad();
        },
    },
};
window.onload = function () {
    Event.run.resizeCreditForm(), Event.run.generatePaypalButton(), Event.run.onLoad(), Event.listener.message();
};
const handler = {
    createOrderExpressCheckout: async function (e, a) {
        return handler.wait(['cart.orderSession.sessionId']).then(async function (e) {
            e = e.totalPrice;
            return a.order.create({ purchase_units: [{ amount: { currency_code: CURRENCY_CODE, value: e.toFixed(2) } }], application_context: APPLICATION_CONTEXT });
        });
    },
    createOrder: function (e, a) {
        var o = model.get.formInfo(),
            t = o['shippingInfo'],
            o = o['totalPrice'],
            n = { email_address: t[FORM_INFO_INTERFACE.email], name: { surname: t[FORM_INFO_INTERFACE.lastName], given_name: t[FORM_INFO_INTERFACE.firstName] } },
            r = { ...APPLICATION_CONTEXT },
            s = [],
            o =
                (helper.isset(t[FORM_INFO_INTERFACE.country]) && helper.isset(t[FORM_INFO_INTERFACE.city])
                    ? (n.address = {
                          country_code: t[FORM_INFO_INTERFACE.countryCode],
                          address_line1: t[FORM_INFO_INTERFACE.address],
                          address_line2: t[FORM_INFO_INTERFACE.apartment],
                          admin_area_1: t[FORM_INFO_INTERFACE.province],
                          admin_area_2: t[FORM_INFO_INTERFACE.city],
                          postal_code: t[FORM_INFO_INTERFACE.zipCode],
                      })
                    : (r.shipping_preference = 'NO_SHIPPING'),
                helper.isset(t[FORM_INFO_INTERFACE.phoneNumber]) && (n.phone = { phone_type: 'HOME', phone_number: { national_number: t[FORM_INFO_INTERFACE.phoneNumber].replace(/[^0-9]+/g, '') } }),
                s.push({
                    amount: { currency_code: CURRENCY_CODE, value: o.toFixed(2) },
                    shipping: {
                        address: {
                            country_code: t[FORM_INFO_INTERFACE.countryCode],
                            address_line1: t[FORM_INFO_INTERFACE.address],
                            address_line2: t[FORM_INFO_INTERFACE.apartment],
                            admin_area_1: t[FORM_INFO_INTERFACE.province],
                            admin_area_2: t[FORM_INFO_INTERFACE.city],
                            postal_code: t[FORM_INFO_INTERFACE.zipCode],
                        },
                        name: { full_name: t[FORM_INFO_INTERFACE.firstName] + ' ' + t[FORM_INFO_INTERFACE.lastName] },
                    },
                }),
                a.order.create({ intent: 'CAPTURE', purchase_units: s, payer: n, application_context: r }));
        return o;
    },
    onApprove: function (e) {
        helper.postMessage(POST_MESSAGE_PARAMS.paypalApprovedOrder, { ...e, isExpressCheckout: model.get.isExpressCheckout() });
    },
    onInit: function (e, a) {
        view.hideLoading(),
            view.showButton(),
            model.get.isExpressCheckout() ||
                (a.disable(),
                model.set.actions(a),
                setInterval(() => {
                    helper.postMessage(POST_MESSAGE_PARAMS.isValidForm, '*');
                }, 150));
    },
    onClick: function (e, a) {
        e = e.fundingSource;
        if (!model.get.isValidForm() && !model.get.isExpressCheckout()) return helper.postMessage(POST_MESSAGE_PARAMS.checkForm, '*'), a.reject();
        switch (e) {
            case 'paypal':
                view.hideButton();
                helper.postMessage(POST_MESSAGE_PARAMS.paypalOpenPopup, '*');
                break;
            case 'card':
                helper.postMessage(POST_MESSAGE_PARAMS.paypalOpenCreditForm, document.querySelector(PAYPAL_BUTTON_ELEMENT_ID).scrollHeight);
                break;
            default:
                break;
        }
    },
    onCancel: function () {
        view.showButton(), helper.postMessage(POST_MESSAGE_PARAMS.paypalClosePopup, '*');
    },
    onShippingChange: function (data, actions) {
        helper.postMessage(POST_MESSAGE_PARAMS.paypalShippingChange, data);
    },
    onError: function () {
        view.showButton(), helper.postMessage(POST_MESSAGE_PARAMS.paypalClosePopup, '*');
    },
    onLoad: function () {
        helper.postMessage(POST_MESSAGE_PARAMS.loaded, '*'), view.hideButton();
    },
    receiverMessage: function (e) {
        'object' == typeof e.data && e.data.name === POST_MESSAGE_PARAMS.sendOrderInfo && model.set.formInfo(e.data.params),
            'object' != typeof e.data ||
                e.data.name !== POST_MESSAGE_PARAMS.isValidForm ||
                model.get.isExpressCheckout() ||
                (model.set.isValidForm(e.data.params), e.data.params ? model.get.actions().enable() : model.get.actions().disable());
    },
    wait: async function (t) {
        return new Promise((e) => {
            const o = setInterval(function () {
                const a = model.get.formInfo();
                t.every((e) => !!helper.getValue(a, e)) && (e(a), clearInterval(o));
            }, 100);
        });
    },
};
const view = {
    hideButton: function () {
        document.querySelector(PAYPAL_BUTTON_ELEMENT_ID).style.display = 'none';
    },
    showButton: function () {
        document.querySelector(PAYPAL_BUTTON_ELEMENT_ID).style.display = 'block';
    },
    showLoading: function () {
        document.querySelector(LOADING_ELEMENT_ID).style.display = 'block';
    },
    hideLoading: function () {
        document.querySelector(LOADING_ELEMENT_ID).style.display = 'none';
    },
};
const model = {
    initial: { formInfo: {}, actions: null, isValidForm: !1, expressCheckout: !!helper.getParameter(PAYPAL_EXPRESS_CHECKOUT) },
    get: {
        formInfo: function () {
            return model.initial.formInfo;
        },
        actions: function () {
            return model.initial.actions;
        },
        isValidForm: function () {
            return model.initial.isValidForm;
        },
        isExpressCheckout: function () {
            return model.initial.expressCheckout;
        },
    },
    set: {
        formInfo: function (i) {
            model.initial.formInfo = i;
        },
        actions: function (i) {
            model.initial.actions = i;
        },
        isValidForm: function (i) {
            model.initial.isValidForm = i;
        },
    },
};
