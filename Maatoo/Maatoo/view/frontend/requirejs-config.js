var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Maatoo_Maatoo/js/order/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Maatoo_Maatoo/js/order/set-payment-information-mixin': true
            },
        }
    }
};
