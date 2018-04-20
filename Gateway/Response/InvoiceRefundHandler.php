<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;

class InvoiceRefundHandler extends HandlerAbstract
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

        //@todo: implement logic when it is needed
    }
}
