<?php

namespace SR\Cardcom\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;
use SR\Cardcom\Controller\CheckoutAbstract;

class PaymentStep extends CheckoutAbstract
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $httpResult = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        try {
            $this->initCheckout();

            if ($iframeSourceUrl = $this->checkout->getIframeSourceUrl()) {
                $this->checkout->placeOrder();
            }

            $this->coreRegistry->register('cardcom_iframe_source_url', $iframeSourceUrl);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $httpResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $httpResult->setPath('checkout/cart');
        }

        return $httpResult;
    }
}
