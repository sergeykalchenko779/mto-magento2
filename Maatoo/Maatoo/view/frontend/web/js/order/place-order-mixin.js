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

        /** Override default place order action */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            costCenterNumberAssigner(paymentData);

            return originalAction(paymentData, messageContainer);
        });
    };
});
