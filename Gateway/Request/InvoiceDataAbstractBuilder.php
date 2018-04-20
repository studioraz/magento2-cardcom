<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Model\Order\Item as OrderItem;
use SR\Cardcom\Gateway\Config\Config;

abstract class InvoiceDataAbstractBuilder extends DataBuilderAbstract
{
    /**
     * Customer name on the invoice
     *
     * Mandatory: Yes
     * Format: string "yaniv"
     */
    const CUSTOMER_NAME = 'InvoiceHead.CustName';

    /**
     * Tall BillGold system if the email with invoice should be sent to the customer
     *
     * Mandatory: Yes
     * Format: string "true"|"false"
     */
    const SHOULD_EMAIL_BE_SENT = 'InvoiceHead.SendByEmail';

    /**
     * Invoice language
     *
     * Mandatory: Yes
     * Format: string "he"|"en"
     */
    const LANGUAGE_CODE = 'InvoiceHead.Language';

    /**
     * Email address where the invoice will be sent to
     *
     * Mandatory: No
     * Format: string "yaniv@site.com"
     */
    const EMAIL = 'InvoiceHead.Email';

    /**
     * Customer address, First line
     * Customer address, Second line
     *
     * Mandatory: No
     * Format: string "Saharov 22"|"P.O. 1234"
     */
    const CUSTOMER_ADDRESS_LINE_ONE = 'InvoiceHead.CustAddresLine1';
    const CUSTOMER_ADDRESS_LINE_TWO = 'InvoiceHead.CustAddresLine2';
    const CUSTOMER_CITY = 'InvoiceHead.CustCity';
    const CUSTOMER_PHONE_NUMBER = 'InvoiceHead.CustLinePH';
    const CUSTOMER_MOBILE_NUMBER = 'InvoiceHead.CustMobilePH';
    const CUSTOMER_COMPANY_ID = 'InvoiceHead.CompID';

    /**
     * Invoice Comments - will be printed on the invoice
     *
     * Mandatory: No
     * Format: string "Test Invoice comment"
     */
    const COMMENTS = 'InvoiceHead.Comments';

    /**
     * Invoice currency Applies only when using Invoice with no charge,
     * when using Invoice with charge the currency will be determined by the credit billing
     *
     * Mandatory: No
     * Format: string "1"|"2" (1 - NIL/ILS, 2 - USD)
     */
    const CURRENCY_CODE = 'InvoiceHead.CoinID';

    /**
     * Determine id the invoice without VAT - suited for customer from overseas
     *
     * Mandatory: No
     * Format: string "true"|"false"
     */
    const IS_VAT_EXCLUDED = 'InvoiceHead.ExtIsVatFree';

    /**
     * Manual counting of invoice, can only be used when creating invoice without charge.
     * Special permission is required in order to use this Read remarked at the bottom
     *
     * Mandatory: No
     */
    const MANUAL_NUMBER = 'InvoiceHead.ManualInvoiceNumber';

    /**
     * Product description
     *
     * Mandatory: Yes
     * Format: string "Intel Computer"
     */
    const ITEM_DESCRIPTION = 'InvoiceLines.Description';

    /**
     * Unit price of product
     *
     * Mandatory: Yes
     * Format: string "1559"
     */
    const ITEM_PRICE = 'InvoiceLines.Price';

    /**
     * Unit quantity
     *
     * Mandatory: Yes
     * Format: string "1"
     */
    const ITEM_QTY = 'InvoiceLines.Quantity';

    /**
     * Not in use must be true
     *
     * Mandatory: Yes
     * Format: string "true"|"false"
     */
    const ITEM_IS_VAT_INCLUDED = 'InvoiceLines.IsPriceIncludeVAT';

    /**
     * Product code - recommended for creating reports
     *
     * Mandatory: No
     * Format: string "ZZASA AASSA - 12"
     */
    const ITEM_PRODUCT_ID = 'InvoiceLines.ProductID';

    /**
     * Allows mix products with VAT and without in one invoice
     * * in most cases the price is with VAT
     * * in case that the invoice is without VAT at all use InvoiceHead.ExtIsVatFree for the invoice head
     *
     * Mandatory: No
     * Format: string "false"|"true"
     */
    const ITEM_IS_VAT_FREE = 'InvoiceLines.IsVatFree';

    /**
     * @var \Zend_Filter_Alnum
     */
    private $alnumFilter;

    /**
     * List of paramters which are used for Invoice Item builder
     *
     * @var array
     */
    private $itemsParameters = [
        self::ITEM_DESCRIPTION,
        self::ITEM_PRICE,
        self::ITEM_QTY,
        self::ITEM_IS_VAT_INCLUDED,
        self::ITEM_PRODUCT_ID,
        self::ITEM_IS_VAT_FREE,
    ];

    /**
     * InvoiceDataBuilder constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->alnumFilter = new \Zend_Filter_Alnum(true);
        parent::__construct($config);
    }

    /**
     * Checks if BUILDER can be used
     *
     * @param int|null $storeId
     * @return mixed
     */
    abstract protected function canBuilderBePerformed($storeId);

    /**
     * Creates Extra Items Stub (ex: Shipping, Tax, Discount etc)
     *
     * such logic is a hook to correct calculation of Grand Total Amount (Order Validation)
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return DataObject|null
     */
    abstract protected function createExtraItems(OrderAdapterInterface $orderAdapter);

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $amount = $buildSubject['amount'];

        /** @var OrderAdapterInterface $order */
        $order = $paymentDO->getOrder();

        /** @var AddressAdapterInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();

        if (!$this->canBuilderBePerformed($order->getStoreId())) {
            return [];
        }

        return array_replace_recursive(
            [
                self::CUSTOMER_NAME => $billingAddress->getFirstname() . ' ' . $billingAddress->getLastname(),
                self::SHOULD_EMAIL_BE_SENT => 'false',
                self::LANGUAGE_CODE => $this->config->getInvoiceLanguageCode($order->getStoreId()),
                self::EMAIL => $billingAddress->getEmail(),
                self::CUSTOMER_ADDRESS_LINE_ONE => $billingAddress->getStreetLine1(),
                self::CUSTOMER_ADDRESS_LINE_TWO => $billingAddress->getStreetLine2(),
                self::CUSTOMER_CITY => $billingAddress->getCity(),
                self::CUSTOMER_PHONE_NUMBER => '',
                self::CUSTOMER_MOBILE_NUMBER => $billingAddress->getTelephone(),
                self::CUSTOMER_COMPANY_ID => (string) $billingAddress->getCompany(),
                self::COMMENTS => '',
                self::CURRENCY_CODE => $this->getCoinID($order),//"1" - NIS, "2" - USD
                self::IS_VAT_EXCLUDED => 'false',
                self::MANUAL_NUMBER => $this->getUniqueId($order),
            ],
            $this->getItemsSection($order)
        );
    }

    /**
     * Returns section of Invoice Items
     *
     * @param OrderAdapterInterface $order
     * @return array
     * @throws LocalizedException
     */
    private function getItemsSection(OrderAdapterInterface $order)
    {
        $itemsSection = [];
        $itemIndex = 1;

        $items = array_merge($order->getItems(), $this->createExtraItems($order));

        /** @var QuoteItem|OrderItem|DataObject $item */
        foreach ($items as $item) {
            if (!$this->canItemBeProcessed($item)) {
                continue;
            }

            foreach ($this->itemsParameters as $parameter) {
                list($paramPrefix, $paramName) = explode('.', $parameter);

                $key = $paramPrefix . $itemIndex . '.' . $paramName;
                $itemsSection[$key] = $this->getItemValueByParamCode($item, $parameter);
            }

            $itemIndex++;
        }

        return $itemsSection;
    }

    /**
     * @param DataObject $item
     * @param string $paramCode
     * @return string
     * @throws LocalizedException
     */
    private function getItemValueByParamCode(DataObject $item, $paramCode)
    {
        switch ($paramCode) {
            case self::ITEM_DESCRIPTION:
                $value = $this->alnumFilter->filter($item->getName());
                break;

            case self::ITEM_PRICE:
                //$value = (string) round($item->getPrice() * 100);
                $value = number_format($item->getPrice(), 2, '.', '');
                break;

            case self::ITEM_QTY:
                $value = (string) $this->getItemQuantity($item);
                break;

            case self::ITEM_IS_VAT_INCLUDED:
                $value = 'true';
                break;

            case self::ITEM_PRODUCT_ID:
                $value = $this->alnumFilter->filter($item->getSku());
                break;

            case self::ITEM_IS_VAT_FREE:
                $value = 'false';
                break;

            default:
                $value = '';
        }

        return $value;
    }

    /**
     * Item Qty getter wrapper
     *
     * @param QuoteItem|OrderItem|DataObject $item
     * @return int|float|string
     */
    protected function getItemQuantity($item)
    {
        return $item->getQty();
    }

    /**
     * Checks if Item can be processed
     *
     * @param QuoteItem|OrderItem|DataObject $item
     * @return bool
     */
    protected function canItemBeProcessed($item)
    {
        return (bool)(float)$item->getPrice();
    }
}
