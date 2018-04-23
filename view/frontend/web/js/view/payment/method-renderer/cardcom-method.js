/* @api */
define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/full-screen-loader'
], function (Component, additionalValidators, fullScreenLoader) {
    'use strict';

    var CardcomPaymentConfig = window.checkoutConfig.payment.cardcom;

    return Component.extend({
        defaults: {
            template: 'SR_Cardcom/payment/cardcom'
        },

        /**
         * @origin Magento_Checkout/js/view/payment/default::placeOrder
         *
         * @override
         */
        placeOrder: function (data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }

            if (this.validate() && additionalValidators.validate()) {
                this.isPlaceOrderActionAllowed(false);

                this.getPlaceOrderDeferredObject()
                    .fail(function () {
                        self.isPlaceOrderActionAllowed(true);
                    })
                    .done(function () {
                        self.afterPlaceOrder();

                        if (self.redirectAfterPlaceOrder) {
                            fullScreenLoader.startLoader();
                            window.location.replace(CardcomPaymentConfig.urls.dedicatedPaymentStep);
                        }
                    });

                return true;
            }

            return false;
        }
    });
});
