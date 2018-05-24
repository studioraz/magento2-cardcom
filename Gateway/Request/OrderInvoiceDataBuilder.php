<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\DataObject;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\Quote\Address as QuoteShippingAddress;

class OrderInvoiceDataBuilder extends InvoiceDataAbstractBuilder
{
    /**
     * Additional options if you pass invoice details
     *
     * Possible values:
     * 0 - Do not create invoices and do not display;
     * 1 - Create an invoice according to the details of the transferred invoice. Default if you transfer invoice details
     * 2 - View invoice details but do not create an invoice
     *
     * Mandatory: No
     * Format: string "0"|"1"|"2"
     */
    const INVOICE_HEAD_OPERATION = 'InvoiceHeadOperation';

    /**
     * Whether to display and enable invoice values for the customer.
     * Active if you produce an invoice. Enter details for invoice.
     *
     * Mandatory: No
     * Format: string "true"|"false"
     */
    const INVOICE_SHOW_HEAD = 'ShowInvoiceHead';

    /**
     * @inheritdoc
     */
    protected function canBuilderBePerformed($storeId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function getInvoiceHeadSection(OrderAdapterInterface $orderAdapter)
    {
        return [
            self::INVOICE_HEAD_OPERATION => $this->getInvoiceHeadOperationValue($orderAdapter),
        ];
    }

    /**
     * @inheritdoc
     */
    protected function createExtraItems(OrderAdapterInterface $orderAdapter)
    {
        $items = $orderAdapter->getItems();

        /** @var QuoteItem $firstItem */
        $firstItem = current($items);

        /** @var Quote $order */
        $quote = $firstItem->getQuote();

        /** @var QuoteShippingAddress $shipping */
        $shipping = $quote->getShippingAddress();

        return [
            $this->createShippingItem($shipping),
            $this->createTaxItem($shipping),
        ];
    }

    /**
     * Creates Shipping Item Stub
     *
     * @param QuoteShippingAddress $shipping
     * @return DataObject
     */
    private function createShippingItem(QuoteShippingAddress $shipping)
    {
        return new DataObject([
            'name' => __('Shipping: ' . $shipping->getShippingDescription()),
            'price' => $shipping->getShippingAmount(),
            'qty' => 1,
            'sku' => '000000',
        ]);
    }

    /**
     * Creates Tax Item Stub
     *
     * @param QuoteShippingAddress $shipping
     * @return DataObject
     */
    private function createTaxItem(QuoteShippingAddress $shipping)
    {
        return new DataObject([
            'name' => __('Tax'),
            'price' => $shipping->getTaxAmount(),
            'qty' => 1,
            'sku' => '001100',
        ]);
    }

    /**
     * Returns value for self::INVOICE_HEAD_OPERATION parameter
     *
     * @param OrderAdapterInterface $order
     * @return string
     */
    private function getInvoiceHeadOperationValue(OrderAdapterInterface $order)
    {
        $value = 1;//default value for this param

        if (!$this->config->isInvoiceCreationActive($order->getStoreId())) {
            $value = 2;
        }

        return (string) $value;
    }
}
