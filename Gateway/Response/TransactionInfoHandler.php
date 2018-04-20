<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;

class TransactionInfoHandler extends HandlerAbstract
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

        $payment->setCcOwner($handledResult['CardOwnerID']);
        $payment->setCcExpYear($handledResult['CardValidityYear']);
        $payment->setCcExpMonth($handledResult['CardValidityMonth']);

        $payment->setAdditionalInformation('terminalnumber', $handledResult['terminalnumber']);
        $payment->setAdditionalInformation('status_code', $handledResult['OperationResponse']);
        $payment->setAdditionalInformation('status_text', $handledResult['OperationResponseText']);
        $payment->setAdditionalInformation('operation', $handledResult['Operation']);

        $payment->setAdditionalInformation('cc_owner_id', $payment->getCcOwner());
        $payment->setAdditionalInformation('cc_exp_date', $payment->getCcExpMonth() . ' / ' . substr($payment->getCcExpYear(), -2));
    }
}
