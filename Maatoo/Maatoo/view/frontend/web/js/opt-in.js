define([
    'Magento_Ui/js/form/element/single-checkbox'
], function(Component) {
    'use strict';
    return Component.extend({

        defaults: {
            value: 0,
            valueMap: {false: '', true: 'I want to receive emails with special offers'}
        },

        initObservable: function () {
            this._super();
            this.observe('value');
            return this;
        },

        initialize: function () {
            this._super();
            return this;
        },

        getOptInText: function () {
            return checkoutConfig.maatoo.opt_in_text;
        },

        isEnable: function () {
            if(window.checkoutConfig.maatoo.opt_in == 1) {
                return true;
            }
            return false;
        },

        onExtendedValueChanged: function (newExtendedValue) {
            this._super(newExtendedValue);
        }

    });
});
