<?php

namespace SR\Cardcom\Controller\Checkout;

use SR\Cardcom\Controller\CheckoutAbstract;

class PaymentSuccess extends CheckoutAbstract
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->initCheckout();
        $this->initTransactionId();

        $order = $this->checkout->placeOrder($this->transactionId);
        //@todo: complete the logic (render)
    }
}
