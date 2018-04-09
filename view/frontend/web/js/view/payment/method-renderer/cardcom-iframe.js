define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/customer-data',
    'mage/url'
], function ($, Component, additionalValidators, quote, customerData, url) {
    'use strict';

    var ccPaymentConfig = window.checkoutConfig.payment.cardcom;

    var loadingProcess = function(state) {
        var actionName = state && state === 'show' ? 'processStart' : 'processStop';
        $('body').trigger(actionName);
        
        return this;
    };

    // start: interaction with iFrame
    window.cardcom = {Iframe:{}};
    window.cardcom.Iframe.handleResponse = function(response) {
        if (response.responseCode === 'cancel' || response.responseCode === 'error') {
            loadingProcess('show');
            window.location.reload(true);
        } else if (response.responseCode === 'success') {
            loadingProcess('show');
            window.location.replace(url.build('checkout/onepage/success/'));
        }
    };
    // end: interaction with iframe

    // start: handle CC_Token option value onChange
    var togglePaymentButtonState = function(e) {
        var isNew = ($(this).val() === '00');
        $('#sr-cardcom-payment-method-note').toggle(isNew);
        $('.sr-btn-iframe').toggle(isNew);
        $('.sr-btn-place-order').toggle(!isNew);
    };
    $(document).on('change', '.sr-cardcom-card-option', togglePaymentButtonState);
    // end: handle CC_Token option value onChange


    return Component.extend({
        redirectAfterPlaceOrder : false, // prevent from redirecting to default success page

        defaults: {
            template: 'SR_Cardcom/payment/cardcom-iframe'
        },

        showIframeElement: function(ui, event) {
            this.getIframeElement();
        },

        getIframeElement: function() {
            var self = this;

            this.renewIframeElement();

            clearTimeout(timerId);
            var timerId = setTimeout(function() {
                self.renewIframeElement();
            }, 600000);

            return 'Please wait.... content is loading...';
        },

        /**
         * Creates new or Updates iFrame html element
         *
         * @returns {this}
         */
        renewIframeElement: function() {
            var self = this;
            this.generateIframeSrc()
                .done(function(response){
                    if (response.data && response.data.iframeSrc) {
                        self.renderIframeElement({"src":response.data.iframeSrc});
                    }
                })
                .always(function(){
                    loadingProcess('hide');
                });
            return this;
        },

        /**
         *  Builds and Renders iFrame element using extraAttributes
         *
         * @param {JSON} extraAttributes
         * @returns {this}
         */
        renderIframeElement: function(extraAttributes) {
            var self = this;
            var attributes = $.extend({
                "scrolling": "no",
                "frameborder": "no",
                "height": "600",
                "width": "600"
            }, extraAttributes);

            var iframeElement = $('<iframe>').attr(attributes);
            var iframeContainer = $('#sr-cardcom-iframe-container');
            var actionToolbarContainer = $('#sr-cardcom-actions-toolbar');
            var noteContainer = $('#sr-cardcom-payment-method-note');


            loadingProcess('show');

            iframeElement.load(function() {
                loadingProcess('hide');

                actionToolbarContainer.hide();
                noteContainer.hide();
                iframeContainer.show();
            });

            iframeContainer.html(iframeElement);

            return this;
        },

        /**
         * Generates iFrame src via ajax request to the server
         *
         * @returns {jQuery Deferred}
         */
        generateIframeSrc: function() {
            var self = this;
            return $.ajax({
                type: "POST",
                url: url.build('cardcom/iframe/form/'),
                dataType: 'json',
                beforeSend: function() {
                    loadingProcess('show');
                },
                data: {
                    email: quote.guestEmail || '',
                    'cardcom_cc_can_token_be_saved': $('.sr-chkbox-save-cc:checked').val() || 0
                }
            });
        },

        /**
         * 
         * @param data
         * @param event
         * @returns {boolean}
         */
        placeOrderWithCC: function(data, event) {
            var self = this;

            if (event) {
                event.preventDefault();
            }

            $.ajax({
                type: "POST",
                url: url.build('cardcom/checkout/placeorder/'),
                dataType: 'json',
                beforeSend: function() {
                    loadingProcess('show');
                },
                data: {
                    'cardcom_cc_token': $('.sr-cardcom-card-option:checked').val() || '00'
                }
            }).done(function(response){
                if (response.data && response.data.redirectUrl) {
                    window.location.replace(response.data.redirectUrl);
                }
            }).always(function(response){
                if (response.errorCode !== 0) {
                    loadingProcess('hide');
                }
            });

            return false;
        },

        /**
         *
         * @returns {*}
         */
        getAvailableCards: function() {
            return ccPaymentConfig.customerCcTokenList;
        },

        /**
         * Returns Image Url
         *
         * @returns {string|null}
         */
        getImageUrl: function() {
            return '';
            return ccPaymentConfig.image;
        },

        /**
         * Checks if payment method has Image
         *
         * @returns {boolean}
         */
        hasImage: function() {
            return !!this.getImageUrl();
        },

        /**
         * Checks is Tokenization is enabled (active)
         *
         * @returns {boolean}
         */
        isTokenizationActive: function() {
            return !!ccPaymentConfig.isTokenizationActive;
        }
    });
});
