<?php

namespace SR\Cardcom\Controller;

use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\App\Action\Context;
use SR\Cardcom\Gateway\Request\LowProfileCodeDataBuilder;
use SR\Cardcom\Model\Checkout;
use SR\Cardcom\Model\CheckoutFactory;

abstract class CheckoutAbstract extends AppAction
{
    /**
     * @var CheckoutFactory
     */
    private $checkoutFactory;

    /**
     * @var Checkout
     */
    protected $checkout;

    /**
     * @var string
     */
    protected $transactionId;

    /**
     * Form constructor.
     * @param Context $context
     * @param CheckoutFactory $checkoutFactory
     */
    public function __construct(
        Context $context,
        CheckoutFactory $checkoutFactory
    ) {
        $this->checkoutFactory = $checkoutFactory;

        parent::__construct($context);
    }

    /**
     * @return $this
     */
    protected function initCheckout()
    {
        if (is_null($this->checkout)) {
            $this->checkout = $this->checkoutFactory->create();
        }
        return $this;
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function initTransactionId()
    {
        if (is_null($this->transactionId)) {
            $this->transactionId = $this->getRequest()->getParam(LowProfileCodeDataBuilder::TRANSACTION_ID);
        }

        if (!$this->transactionId) {
            throw new \Exception('Cardcom checkout Transaction Id is required.');
        }

        return $this;
    }
}
