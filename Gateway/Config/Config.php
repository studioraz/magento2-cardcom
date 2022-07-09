<?php

namespace SR\Cardcom\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Config\Config as PaymentGatewayConfig;
use Magento\Store\Model\ScopeInterface;
use SR\Cardcom\Model\System\Config\Source\Operation;

class Config extends PaymentGatewayConfig
{
    /**
     * XML Config parts
     * ex: 'payment/cardcom/{KEY_}'
     */
    const KEY_ACTIVE = 'active';
    const KEY_API_USERNAME = 'api_username';
    const KEY_API_PASSWORD = 'api_password';
    const KEY_LANGUAGE_CODE = 'language_code';
    const KEY_MODE = 'mode';
    const KEY_OPERATION = 'operation';
    const KEY_TERMINAL_NUMBER = 'terminal_number';
    const KEY_TERMINAL_NUMBER_TOKENIZATION = 'terminal_number_tokenization';
    const KEY_USE_INVOICE_CREATION = 'use_invoice_creation';
    const KEY_USE_INVOICE_SHIPPING_DESCRIPTION = 'invoice_shipping_description';
    const KEY_INVOICE_LANGUAGE_CODE = 'invoice_language_code';
    const KEY_REDIRECT_TYPE = 'redirect_type';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param null $methodCode
     * @param string $pathPattern
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);

        $this->scopeConfig = $scopeConfig;
    }

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
     * Returns redirect type
     * @param $storeId
     * @return mixed|null
     */
    public function getRedirectType($storeId = null)
    {
        return $this->getValue(self::KEY_REDIRECT_TYPE, $storeId);
    }

    /**
     * Returns Terminal Number
     *
     * @param int|null $storeId
     * @return string
     */
    public function getTerminalNumber($storeId = null)
    {
        $paramKey = self::KEY_TERMINAL_NUMBER;
        if (in_array($this->getOperationId($storeId), [Operation::BILLING_AND_TOKEN_CREATION, Operation::TOKEN_CREATION_ONLY])) {
            $paramKey = self::KEY_TERMINAL_NUMBER_TOKENIZATION;
        }

        return trim($this->getValue($paramKey, $storeId));
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
     * Returns API Password
     *
     * @param int|null $storeId
     * @return string
     */
    public function getApiPassword($storeId = null)
    {
        return $this->getValue(self::KEY_API_PASSWORD, $storeId);
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
     * he - Hebrew, en - English, ...
     *
     * @param int|null $storeId
     * @return mixed
     */
    public function getLanguageCode($storeId = null)
    {
        $localeLanguageCode = $this->scopeConfig->getValue('general/locale/code', ScopeInterface::SCOPE_STORE, $storeId);
        $list = ['en_US' => 'en', 'he_IL' => 'he', ];

        $languageCode = $this->getValue(self::KEY_LANGUAGE_CODE, $storeId);
        if (isset($list[$localeLanguageCode])) {
            $languageCode = $list[$localeLanguageCode];
        }

        return $languageCode;
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
        return $this->getLanguageCode($storeId);
    }

    /**
     * Get custom shipping description for invoice shipping line
     * @param int|null $storeId
     * @return mixed
     */
    public function getInvoiceShippingDescription($storeId = null) {
        return $this->getValue(self::KEY_USE_INVOICE_SHIPPING_DESCRIPTION, $storeId);
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

    /**
     * Adapts and returns Transaction Id value without '-' characters.
     *
     * "a2a14740-0cc0-4b50-b98f-5c990ab8a3f8" (36 chars) => "a2a147400cc04b50b98f5c990ab8a3f8" (32 chars)
     *
     * @param string $rawValue
     * @return string 32 Characters
     */
    public function getAdaptedTransactionId($rawValue)
    {
        return str_replace('-', '', $rawValue);
    }

    /**
     * Returns Full Formatted (original) Transaction Id value with '-' characters
     *
     * "a2a147400cc04b50b98f5c990ab8a3f8" (32 chars) => "a2a14740-0cc0-4b50-b98f-5c990ab8a3f8"
     *
     * @param string $adaptedValue
     * @return string 36 Characters
     */
    public function getFullFormattedTransactionId($adaptedValue)
    {
        $segments = [];
        $segments[] = substr($adaptedValue, 0, 8);
        $segments[] = substr($adaptedValue, 8, 4);
        $segments[] = substr($adaptedValue, 12, 4);
        $segments[] = substr($adaptedValue, 16, 4);
        $segments[] = substr($adaptedValue, 20, 12);

        return implode('-', $segments);
    }
}
