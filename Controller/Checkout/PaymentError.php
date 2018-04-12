<?php

namespace SR\Cardcom\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;
use SR\Cardcom\Controller\CheckoutAbstract;

class PaymentError extends CheckoutAbstract
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $httpResult = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $httpResult;
    }
}
