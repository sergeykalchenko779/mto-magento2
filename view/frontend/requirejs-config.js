var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Maatoo_Maatoo/js/order/place-order-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Maatoo_Maatoo/js/order/set-payment-information-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Maatoo_Maatoo/js/set-birthday-mixin': true
            },
            'Magento_Checkout/js/model/shipping-rate-processor/new-address': {
                'Maatoo_Maatoo/js/set-birthday-into-customer-address-mixin': true
            },
        }
    }
};
