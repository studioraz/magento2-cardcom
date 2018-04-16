<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;

class TransactionIdHandler extends HandlerAbstract
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

        $transId = $handledResult['lowprofilecode'];

        // trick for Quote/Order Payment.last_trans_id parameter.
        // This parameter can be saved into DB with 32 characters ONLY
        // Cardcom returns 36 characters (32 + 4 dashes)
        $adaptedTransId = $this->config->getAdaptedTransactionId($transId);
        $payment->setLastTransId($adaptedTransId);
        $payment->setAdditionalInformation('last_trans_id', $adaptedTransId);

        // Save Full Formatted (original) value
        $payment->setAdditionalInformation('lowprofilecode', $transId);
    }
}
