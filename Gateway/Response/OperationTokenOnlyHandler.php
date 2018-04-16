<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Model\System\Config\Source\Operation;

class OperationTokenOnlyHandler extends HandlerAbstract
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

        if ((int)$handledResult['Operation'] !== Operation::TOKEN_CREATION_ONLY) {
            return;
        }
    }
}
