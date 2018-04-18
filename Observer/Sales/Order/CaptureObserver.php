<?php

namespace SR\Cardcom\Observer\Sales\Order;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use SR\Cardcom\Model\System\Config\Source\Operation;

class CaptureObserver implements ObserverInterface
{
    /**
     * @inheritdoc
     *
     * @event sales_order_payment_capture
     *
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var OrderPaymentInterface $orderPayment */
        $orderPayment = $observer->getPayment();

        $operationId = $orderPayment->getAdditionalInformation('operation');
        if (in_array($operationId, [Operation::BILLING_AND_TOKEN_CREATION, Operation::TOKEN_CREATION_ONLY])) {
            // trick: in order to set Order Last Trans Id before Invoice defines its own TransactionId parameter
            $orderPayment->setLastTransId($orderPayment->getAdditionalInformation('last_trans_id'));
        }
    }
}
