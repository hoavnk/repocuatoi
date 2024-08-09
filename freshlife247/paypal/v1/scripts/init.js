const POST_MESSAGE_PARAMS = {
    sendOrderInfo: "paypalSendOrderInfo",
    paypalApprovedOrder: "paypalApprovedOrder",
    paypalCloseCreditForm: "paypalCloseCreditForm",
    paypalOpenCreditForm: "paypalOpenCreditForm",
    paypalOpenPopup: "paypalOpenPopup",
    paypalClosePopup: "paypalClosePopup",
    isValidForm: "isValidForm",
    checkForm: "checkForm",
    loaded: "loaded",
    paypalShippingChange: "paypalShippingChange"
}

const APPLICATION_CONTEXT = {
    brand_name: 'merchant',
    user_action: 'CONTINUE'
}

const CURRENCY_CODE = "USD";
const PAYPAL_BUTTON_ELEMENT_ID = "#paypal-button-container";
const LOADING_ELEMENT_ID = "#loading";
const PAYPAL_EXPRESS_CHECKOUT = "paypal_express_checkout";
const FORM_INFO_INTERFACE = {
    email: "email",
    address: "addressDetail",
    apartment: "apartment",
    city: "city",
    country: "country",
    countryCode: "countryCode",
    firstName: "firstName",
    lastName: "lastName",
    phoneNumber: "phoneNumber",
    province: "province",
    zipCode: "zipCode"
}