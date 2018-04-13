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
    const KEY_API_USERNAME = 'api_username';
    const KEY_LANGUAGE_CODE = 'language_code';
    const KEY_MODE = 'mode';
    const KEY_OPERATION = 'operation';
    const KEY_TERMINAL_NUMBER = 'terminal_number';
    const KEY_USE_INVOICE_CREATION = 'use_invoice_creation';
    const KEY_INVOICE_LANGUAGE_CODE = 'invoice_language_code';



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

    /**
     * Returns payment Mode (iFrame | Redirect)
     *
     * @param int|null $storeId
     * @return string
     */
    public function geMode($storeId = null)
    {
        return $this->getValue(self::KEY_MODE, $storeId);
    }

    /**
     * Returns Language which is used in Transactions.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getLanguageCode($storeId = null)
    {
        return $countryCardTypes = $this->getValue(self::KEY_LANGUAGE_CODE, $storeId);
    }

    /**
     * @param int|null $storeId
     * @return bool
     */
    public function isInvoiceCreationActive($storeId = null)
    {
        return (bool) $this->getValue(self::KEY_USE_INVOICE_CREATION, $storeId);
    }

    /**
     * Returns Language which is used in CC Invoices.
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getInvoiceLanguageCode($storeId = null)
    {
        return $countryCardTypes = $this->getValue(self::KEY_INVOICE_LANGUAGE_CODE, $storeId);
    }

    /**
     * Returns credit card string code by CC_Id
     *
     * @param mixed|null $creditCardId
     * @return string
     */
    public function getCcTypeById($creditCardId = null)
    {
        $creditCardId = (int)$creditCardId;

        $list = [0 => 'PL', 1 => 'MasterCard', 2 => 'Visa', 3 => 'Maestro', 5 => 'Isracard',];
        return isset($list[$creditCardId]) ? $list[$creditCardId] : $list[0];
    }
}
