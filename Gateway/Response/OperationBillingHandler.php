<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Model\System\Config\Source\Operation;

class OperationBillingHandler extends HandlerAbstract
{
    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        /** @var InfoInterface $payment */
        $payment = $paymentDO->getPayment();

        if (!$handledResult = $this->parseResponse($response)) {
            return;
        }

        // Extra params are received when operation is Billing (Charge Only)
        if ((int)$handledResult['Operation'] !== Operation::BILLING) {
            return;
        }

        $this->defineGeneralOperationParams($payment, $handledResult);
    }
}
