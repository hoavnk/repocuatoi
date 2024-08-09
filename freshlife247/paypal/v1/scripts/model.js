const model = {
    initial: {
        formInfo: {},
        actions: null,
        isValidForm: false,
        expressCheckout: !!helper.getParameter(PAYPAL_EXPRESS_CHECKOUT) ? true : false
    },
    get: {
        formInfo: function() {
            return model.initial.formInfo
        },
        actions: function() {
            return model.initial.actions
        },
        isValidForm: function() {
            return model.initial.isValidForm
        },
        isExpressCheckout: function() {
            return model.initial.expressCheckout
        }
    },
    set: {
        formInfo: function(values) {
            model.initial.formInfo = values;
        },
        actions: function(values) {
            model.initial.actions = values
        },
        isValidForm: function(values) {
            model.initial.isValidForm = values
        }
    }
}