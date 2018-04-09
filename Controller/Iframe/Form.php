<?php

namespace SR\Cardcom\Controller\Iframe;

use Magento\Framework\App\Action\Action as AppAction;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use SR\Cardcom\Model\Checkout;
use SR\Cardcom\Model\CheckoutFactory;

class Form extends AppAction
{
    /**
     * @var CheckoutFactory
     */
    private $checkoutFactory;

    /**
     * @var Checkout
     */
    private $checkout;

    public function __construct(
        Context $context,
        CheckoutFactory $checkoutFactory
    ) {
        $this->checkoutFactory = $checkoutFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->getRequest()->isAjax()) {
            throw new NotFoundException(__('Parameter is incorrect.'));
        }

        $controllerResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $responseData = [
            'errorCode' => 0,
            'message' => 'OK',
            'data' => [],
        ];

        try {
            $this->initCheckout();
            $iframeSrc = $this->checkout->getIframeSourceUrl();

            if ($iframeSrc) {
                $responseData['data']['iframeSrc'] = $iframeSrc;
            } else {
                throw new \Exception('We can\'t retrieve Cardcom API EndPoint Url.');
            }
        } catch (\Exception $e) {
            $responseData['errorCode'] = 300;
            $responseData['message'] = __('We can\'t start Cardcom payment process.');
        }

        $controllerResult->setData($responseData);
        return $controllerResult;
    }

    /**
     * @return $this
     */
    private function initCheckout()
    {
        if (is_null($this->checkout)) {
            $this->checkout = $this->checkoutFactory->create();
        }
        return $this;
    }
}
