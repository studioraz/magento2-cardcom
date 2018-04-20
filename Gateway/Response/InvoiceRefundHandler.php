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

        $payment->setAdditionalInformation('invoice_refund_deal_number', $handledResult['InternalDealNumber']);
        $payment->setAdditionalInformation('invoice_refund_number', $handledResult['InvoiceResponse_InvoiceNumber']);
        $payment->setAdditionalInformation('invoice_refund_type', $handledResult['InvoiceResponse_InvoiceType']);
    }
}
