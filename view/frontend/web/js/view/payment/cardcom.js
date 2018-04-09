function customLog(lineNum,value){console.log('=== Start at Line::'+lineNum+' ===');console.log(value);console.log('=== End at Line::'+lineNum+' ===');};
define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'cardcom',
        component: 'SR_Cardcom/js/view/payment/method-renderer/cardcom-iframe'
    });

    /** Add view logic here if needed */
    return Component.extend({});
});
