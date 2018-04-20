<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\DataObject;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;

class CreditMemoDataBuilder extends InvoiceDataAbstractBuilder
{
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
    protected function createShippingItem(OrderAdapterInterface $orderAdapter)
    {
        $items = $orderAdapter->getItems();

        /** @var OrderItem $firstItem */
        $firstItem = current($items);

        /** @var Order $order */
        $order = $firstItem->getOrder();

        return new DataObject([
            'name' => 'Shipping: ' . $order->getShippingDescription(),
            'price' => $order->getShippingRefunded(),
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

        /** @var OrderItem $firstItem */
        $firstItem = current($items);

        /** @var Order $order */
        $order = $firstItem->getOrder();

        return new DataObject([
            'name' => 'Tax',
            'price' => $order->getTaxRefunded(),
            'qty' => 1,
            'sku' => '001100',
        ]);
    }
}
