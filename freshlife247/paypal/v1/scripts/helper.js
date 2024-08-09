const helper = {
    postMessage: function (name, params, targetIframe = "*") {
        parent.postMessage({
            name: name,
            params: params,
            id: helper.getParameter("id")
        }, targetIframe);
    },

    isset: function (value) {
        if ((Array.isArray(value) && value.length > 0) || (!!value)) {
            return true;
        }

        return false
    },

    getParameter: function (name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    },

    formatCurrency: function (number) {
        return (number)?.toLocaleString('en-US', {
            style: 'currency',
            currency: 'USD',
        }) ?? `${0}`
    },

    getValue: function(object, path) {
        return [object]?.concat(path?.split('.'))?.reduce(function(a, b) { return a?.[b] });
    }
}