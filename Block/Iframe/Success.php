<?php

namespace SR\Cardcom\Block\Iframe;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

class Success extends Template
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * Success constructor.
     * @param Context $context
     * @param CheckoutSession $checkoutSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        $this->checkoutSession  = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        return 'Order has been placed successfully.';
    }

    /**
     * @return mixed
     */
    public function getLastRealOrderId()
    {
        return $this->checkoutSession->getLastRealOrderId();
    }

    /**
     * @return mixed
     */
    public function getLastOrderStatus()
    {
        return $this->checkoutSession->getLastOrderStatus();
    }
}
