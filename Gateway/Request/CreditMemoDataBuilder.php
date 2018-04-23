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
    protected function createExtraItems(OrderAdapterInterface $orderAdapter)
    {
        $items = $orderAdapter->getItems();

        /** @var OrderItem $firstItem */
        $firstItem = current($items);

        /** @var Order $order */
        $order = $firstItem->getOrder();

        return [
            $this->createShippingItem($order),
            $this->createTaxItem($order),
            $this->createAdjustmentRefundItem($order),
            $this->createAdjustmentFeeItem($order),
        ];
    }

    /**
     * @param OrderItem|DataObject $item
     * @return int|float|string
     */
    protected function getItemQuantity($item)
    {
        return $item->getQtyRefunded();
    }

    /**
     * @param OrderItem|DataObject $item
     * @return bool
     */
    protected function canItemBeProcessed($item)
    {
        return (bool)((float)$item->getPrice() && (float)$item->getQtyRefunded());
    }

    /**
     * Creates Shipping Item Stub
     *
     * @param Order $order
     * @return DataObject
     */
    private function createShippingItem(Order $order)
    {
        // start: Calculate already refunded Shipping Amount (according to existing Creditmemos)
        $amountRefundedTotal = 0;
        $creditmemos = $order->getCreditmemosCollection();

        /** @var Order\Creditmemo $creditmemoItem */
        foreach ($creditmemos->getItems() as $creditmemoItem) {
            $amountRefundedTotal += (float)$creditmemoItem->getShippingAmount();
        }
        // end: Calculate already refunded Shipping Amount

        $amountToRefund = (float)$order->getShippingRefunded() - $amountRefundedTotal;

        // Total of Refunded Shipping Amount should not be greater than Invoiced Shipping Amount
        if ($amountToRefund >= (float)$order->getShippingInvoiced()) {
            $amountToRefund = 0;
        }

        return new DataObject([
            'name' => __('Shipping: ' . $order->getShippingDescription()),
            'price' => $amountToRefund,
            'qty_refunded' => 1,
            'sku' => '000000',
        ]);
    }

    /**
     * Creates Tax Item Stub
     *
     * @param Order $order
     * @return DataObject
     */
    private function createTaxItem(Order $order)
    {
        return new DataObject([
            'name' => __('Tax'),
            'price' => $order->getTaxRefunded(),
            'qty_refunded' => 1,
            'sku' => '001100',
        ]);
    }

    /**
     * Creates Adjustment Refund Item Stub (Refund operation: adjustment_positive param)
     *
     * @param Order $order
     * @return DataObject
     */
    private function createAdjustmentRefundItem(Order $order)
    {
        return new DataObject([
            'name' => __('Adjustment Refund'),
            'price' => $order->getAdjustmentPositive(),
            'qty_refunded' => 1,
            'sku' => '002200',
        ]);
    }

    /**
     * Creates Adjustment Fee Item Stub (Refund operation: adjustment_negative param)
     *
     * @param Order $order
     * @return DataObject
     */
    private function createAdjustmentFeeItem(Order $order)
    {
        return new DataObject([
            'name' => __('Adjustment Fee'),
            'price' => $order->getAdjustmentNegative() * (-1),
            'qty_refunded' => 1,
            'sku' => '003300',
        ]);
    }
}
