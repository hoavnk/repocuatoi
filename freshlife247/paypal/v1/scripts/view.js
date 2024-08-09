const view = {
    hideButton: function() {
        document.querySelector(PAYPAL_BUTTON_ELEMENT_ID).style.display = "none";
    },
    showButton: function() {
        document.querySelector(PAYPAL_BUTTON_ELEMENT_ID).style.display = "block";
    },
    showLoading: function() {
        document.querySelector(LOADING_ELEMENT_ID).style.display = "block";
    },
    hideLoading: function() {
        document.querySelector(LOADING_ELEMENT_ID).style.display = "none";
    },
}