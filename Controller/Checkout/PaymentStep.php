<?php

namespace SR\Cardcom\Controller\Checkout;

use Magento\Framework\Controller\ResultFactory;
use SR\Cardcom\Controller\CheckoutAbstract;
use \SR\Cardcom\Model\System\Config\Source\RedirectType;

class PaymentStep extends CheckoutAbstract
{
    /**
     * @inheritdoc
     */
    public function execute()
    {

        try {
            $this->initCheckout();

            if ($iframeSourceUrl = $this->checkout->getIframeSourceUrl()) {
                $this->checkout->placeOrder();
            }

            // redirect to Cardcom standalone page
            if ($this->checkout->getRedirectType() == RedirectType::REDIRECT_STANDALONE_PAGE) {
                $redirect = $this->resultRedirectFactory->create();
                $redirect->setPath($iframeSourceUrl);
                $redirect->setPath($iframeSourceUrl);
                return $redirect;
            }

            $httpResult = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $this->coreRegistry->register('cardcom_iframe_source_url', $iframeSourceUrl);

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $httpResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $httpResult->setPath('checkout/cart');
        }

        return $httpResult;
    }
}
