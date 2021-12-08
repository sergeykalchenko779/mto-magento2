/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
    'jquery/jquery.cookie'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            var billingAddress = quote.billingAddress();

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if (billingAddress['extension_attributes'] === undefined) {
                billingAddress['extension_attributes'] = {};
            }

            var attribute = shippingAddress.customAttributes.find(
                function (element) {
                    return element.attribute_code === 'maatoo_opt_in';
                }
            );

            if (attribute && attribute.value) {
                shippingAddress['extension_attributes']['maatoo_opt_in'] = attribute.value;
                billingAddress['extension_attributes']['maatoo_opt_in'] = attribute.value;
                quote.shippingAddress(shippingAddress);
                quote.billingAddress(billingAddress);
            }

            return originalAction(shippingAddress);
        });
    };
});
