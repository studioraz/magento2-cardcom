<?php

namespace SR\Cardcom\Controller\Checkout;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use SR\Cardcom\Controller\CheckoutAbstract;
use SR\Cardcom\Model\CheckoutFactory;
use Magento\Payment\Model\Method\Logger;

class PaymentNotify extends CheckoutAbstract
{
    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * PaymentNotify constructor.
     * @param Context $context
     * @param CheckoutFactory $checkoutFactory
     * @param Registry $coreRegistry
     * @param Logger $customLogger
     */
    public function __construct(
        Context $context,
        CheckoutFactory $checkoutFactory,
        Registry $coreRegistry,
        Logger $customLogger
    ) {
        $this->customLogger = $customLogger;

        parent::__construct($context, $checkoutFactory, $coreRegistry);
    }

    /**
     * @inheritdoc
     *
     * @todo implement ability to Cancel Order on Failure (when it is needed)
     */
    public function execute()
    {
        // sample of the call: https://domainname.dev/cardcom/checkout/paymentnotify/?oid=111-123456

        // sample of the Request Params
        /*$params = [
            '___SID' => 'U',
            'oid' => '111-123456',//{quoteId}-{rand}
            'terminalnumber' => 1000,
            'lowprofilecode' => 'b8986944-032d-4fba-806f-4ef5d9d581f2',
            'Operation' => 4,
            'SuspendedDealResponseCode' => 0,
            'OperationResponse' => 0,
            'OperationResponseText' => 'OK',
        ];*/

        $requestParams = $this->getRequest()->getParams();

        $this->customLogger->debug([
            'callable' => static::class,
            'request_params' => $requestParams,
        ]);

        try {
            $this->initCheckout();
            $this->initTransactionId();

            list($quoteId, $randNum) = explode('-', $requestParams['oid'] . '--');
            $this->checkout->captureOrderAmount($quoteId, $this->transactionId);
        } catch (\Exception $e) {
            $this->customLogger->debug([
                'exception_point' => static::class,
                'exception_message' => $e->getMessage(),
            ]);
        }
    }
}
