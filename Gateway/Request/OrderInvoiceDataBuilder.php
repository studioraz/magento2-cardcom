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
     * @inheritdoc
     */
    protected function canBuilderBePerformed($storeId)
    {
        return $this->config->isInvoiceCreationActive($storeId);
    }

    /**
     * @inheritdoc
     */
    protected function createShippingItem(OrderAdapterInterface $orderAdapter)
    {
        $items = $orderAdapter->getItems();

        /** @var QuoteItem $firstItem */
        $firstItem = current($items);

        /** @var Quote $order */
        $quote = $firstItem->getQuote();

        /** @var QuoteShippingAddress $shipping */
        $shipping = $quote->getShippingAddress();

        return new DataObject([
            'name' => 'Shipping: ' . $shipping->getShippingDescription(),
            'price' => $shipping->getShippingAmount(),
            'qty' => 1,
            'sku' => '000000',
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function createTaxItem(OrderAdapterInterface $orderAdapter)
    {
        $items = $orderAdapter->getItems();

        /** @var QuoteItem $firstItem */
        $firstItem = current($items);

        /** @var Quote $order */
        $quote = $firstItem->getQuote();

        /** @var QuoteShippingAddress $shipping */
        $shipping = $quote->getShippingAddress();

        return new DataObject([
            'name' => 'Tax',
            'price' => $shipping->getTaxAmount(),
            'qty' => 1,
            'sku' => '001100',
        ]);
    }
}
