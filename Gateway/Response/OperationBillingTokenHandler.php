<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Model\System\Config\Source\Operation;

class OperationBillingTokenHandler extends HandlerAbstract
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

        if ((int)$handledResult['Operation'] !== Operation::BILLING_AND_TOKEN_CREATION) {
            return;
        }

        $this->defineGeneralOperationParams($payment, $handledResult);

        $payment->setAdditionalInformation('token_response', $handledResult['TokenResponse']);
        $payment->setAdditionalInformation('token', $handledResult['Token']);
        $payment->setAdditionalInformation('token_exp_date', $handledResult['TokenExDate']);
        $payment->setAdditionalInformation('token_approval_number', $handledResult['TokenApprovalNumber']);
        $payment->setAdditionalInformation('tokef', $handledResult['ExtShvaParams_Tokef30']);
        $payment->setAdditionalInformation('card_token', $handledResult['ExtShvaParams_CardToken']);
    }
}
