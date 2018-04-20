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
}
