<?php

namespace SR\Cardcom\Observer\Sales\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class CaptureObserver implements ObserverInterface
{
    /**
     * @inheritdoc
     *
     * @event sales_order_payment_capture
     */
    public function execute(Observer $observer)
    {
        /** @var OrderPaymentInterface $orderPayment */
        $orderPayment = $observer->getPayment();

        // trick: in order to set Order Last Trans Id before Invoice defines its own TransactionId parameter
        $orderPayment->setLastTransId($orderPayment->getAdditionalInformation('last_trans_id'));
    }
}
