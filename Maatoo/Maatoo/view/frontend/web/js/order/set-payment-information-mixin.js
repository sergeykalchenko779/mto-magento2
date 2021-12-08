define([
    'jquery',
    'mage/utils/wrapper',
    'Maatoo_Maatoo/js/order/maatoo-opt-in-assigner',
], function (
    $,
    wrapper,
    costCenterNumberAssigner,
) {
    'use strict';

    return function (placeOrderAction) {

        /** Override place-order-mixin for set-payment-information action */
        return wrapper.wrap(placeOrderAction, function (originalAction, messageContainer, paymentData) {
            costCenterNumberAssigner(paymentData);

            return originalAction(messageContainer, paymentData);
        });
    };
});
