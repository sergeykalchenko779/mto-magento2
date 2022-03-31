/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            let shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if (shippingAddress.customAttributes) {
                let attribute = shippingAddress.customAttributes.find(
                    function (element) {
                        return element.attribute_code === 'birthday';
                    }
                );

                if (attribute) {
                    shippingAddress['extension_attributes']['birthday'] = attribute.value;
                }
            }

            return originalAction();
        });
    };
});
