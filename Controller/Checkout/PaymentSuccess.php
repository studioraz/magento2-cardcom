<?php

namespace SR\Cardcom\Controller\Checkout;

use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use SR\Cardcom\Controller\CheckoutAbstract;

class PaymentSuccess extends CheckoutAbstract
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $httpResult = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        try {
            //$this->initCheckout();
            //$this->initTransactionId();

            /** @var Order $order */
            //$order = $this->checkout->placeOrder($this->transactionId);
        } catch (\Exception $e) {
            /** @var Forward $httpResult */
            $httpResult = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $httpResult->forward('paymenterror');
        }

        return $httpResult;
    }
}
