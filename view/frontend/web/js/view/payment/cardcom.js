/* @api */
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'cardcom',
        component: 'SR_Cardcom/js/view/payment/method-renderer/cardcom-method'
    });

    /** Add view logic here if needed */
    return Component.extend({});
});
