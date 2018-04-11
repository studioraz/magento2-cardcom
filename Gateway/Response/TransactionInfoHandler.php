<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Gateway\Config\Config;

class TransactionInfoHandler implements HandlerInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * StoreConfigBuilder constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];

        /** @var InfoInterface $payment */
        $payment = $paymentDO->getPayment();

        $rawResponse = isset($response['object']) ? $response['object'] : '';

        if (strpos($rawResponse, '=') !== false) {
            //sample: ResponseCode=0&Description=Low Profile Code Found&terminalnumber=1000&lowprofilecode=7a1735e1-e818-48b4-a1ba-491094355055&Operation=4&ProssesEndOK=0&CardValidityYear=2020&CardValidityMonth=10&CardOwnerID=040617649&NumOfPayments=1&SuspendedDealResponseCode=0&SuspendedDealId=2338&SuspendedDealGroup=1&CallIndicatorResponse=The+remote+name+could+not+be+resolved%3a+%27sr.m19x.de&ReturnValue=145000027&CoinId=2&OperationResponse=0&OperationResponseText=OK
            parse_str($rawResponse, $handledResult);

            $payment->setCcOwner($handledResult['CardOwnerID']);
            $payment->setCcExpYear($handledResult['CardValidityYear']);
            $payment->setCcExpMonth($handledResult['CardValidityMonth']);

            $payment->setAdditionalInformation('last_trans_id', $handledResult['lowprofilecode']);
            $payment->setAdditionalInformation('lowprofilecode', $handledResult['lowprofilecode']);

            $payment->setAdditionalInformation('terminalnumber', $handledResult['terminalnumber']);
            $payment->setAdditionalInformation('status_code', $handledResult['OperationResponse']);
            $payment->setAdditionalInformation('status_text', $handledResult['OperationResponseText']);
            $payment->setAdditionalInformation('operation', $handledResult['Operation']);

            // Extra params are received when operation is Billing (Charge Only)
            if ($handledResult['Operation'] === '1') {
                $payment->setCcLast4($handledResult['ExtShvaParams_CardNumber5']);
                $payment->setCcType($handledResult['ExtShvaParams_Mutag24']);

                $payment->setAdditionalInformation('status_code', $handledResult['ExtShvaParams_Status1']);
                $payment->setAdditionalInformation('jparameter', $handledResult['ExtShvaParams_JParameter29']);
                $payment->setAdditionalInformation('return_value', $handledResult['ReturnValue']);
                $payment->setAdditionalInformation('cc_ss_issue', $handledResult['ExtShvaParams_CardTypeCode60']);

                $payment->setAdditionalInformation('cc_mask_last_4', 'xxxx-' . $handledResult['ExtShvaParams_CardNumber5']);
                $payment->setAdditionalInformation('cc_owner_id', $payment->getCcOwner());
                $payment->setAdditionalInformation('cc_owner_name', $handledResult['CardOwnerName']);
                $payment->setAdditionalInformation('cc_type', $this->config->getCcTypeById($payment->getCcType()));
                $payment->setAdditionalInformation('cc_exp_date', $payment->getCcExpMonth() . ' / ' . substr($payment->getCcExpYear(), -2));
            }
        }
    }
}
