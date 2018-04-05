<?php

namespace SR\Cardcom\Gateway\Config;

use Magento\Payment\Gateway\Config\Config as PaymentGatewayConfig;

class Config extends PaymentGatewayConfig
{
    /**
     * XML Config parts
     * ex: 'payment/cardcom/{KEY_}'
     */
    const KEY_ACTIVE = 'active';
//    const KEY_API_PASSWORD_TOKENIZATION = 'api_password_tokenization';
    const KEY_API_USERNAME = 'api_username';

//    const KEY_API_USERNAME_TOKENIZATION = 'api_username_tokenization';
//    const KEY_COUNTRY_CREDIT_CARD = 'specificcountry';
//    const KEY_DEBUG = 'debug';
//    const KEY_GATEWAY_URL = 'gateway_url';
//    const KEY_IMAGE = 'image';
//    const KEY_INSTALLMENTS_TABLE = 'installments_table';
//    const KEY_INVOICE_SUBJECT = 'invoice_subject';
//    const KEY_INVOICE_COMMENTS = 'invoice_comments';
//    const KEY_LANGUAGE_CODE = 'language_code';
    const KEY_OPERATION = 'operation';
    const KEY_TERMINAL_NUMBER = 'terminal_number';
//    const KEY_TERMINAL_NUMBER_TOKENIZATION = 'terminal_number_tokenization';
//    const KEY_USE_INSTALLMENTS = 'use_installments';
//    const KEY_USE_INVOICE_CREATION = 'use_invoice_creation';
//    const KEY_USE_INVOICE_SHIPPING_ITEM_CODE = 'invoice_shipping_item_code';
//    const KEY_USE_INVOICE_SHIPPING_ITEM_DESCRIPTION = 'invoice_shipping_item_description';
//    const KEY_USE_CC_TOKENIZATION = 'use_cc_tokenization';



    /**
     * Returns payment operation ID.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getOperationId($storeId = null)
    {
        return $this->getValue(self::KEY_OPERATION, $storeId);
    }

    /**
     * Returns Terminal Number
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTerminalNumber($storeId = null)
    {
        return $this->getValue(self::KEY_TERMINAL_NUMBER, $storeId);
    }

    /**
     * Returns API User Name
     *
     * @param int|null $storeId
     * @return string
     */
    public function getApiUsername($storeId = null)
    {
        return $this->getValue(self::KEY_API_USERNAME, $storeId);
    }
}
