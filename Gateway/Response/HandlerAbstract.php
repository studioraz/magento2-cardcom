<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Gateway\Config\Config;

abstract class HandlerAbstract implements HandlerInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * StoreConfigBuilder constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Returns list of parameters of parsed Raw Response
     *
     * @param array $response ['object' => mixed]
     * @return array|null on failure
     */
    protected function parseResponse(array $response)
    {
        $parsedResponse = null;

        $rawResponse = isset($response['object']) ? $response['object'] : '';

        if (strpos($rawResponse, '=') !== false) {
            //sample: ResponseCode=0&Description=Low Profile Code Found&terminalnumber=1000&lowprofilecode=7a1735e1-e818-48b4-a1ba-491094355055&Operation=4...
            parse_str($rawResponse, $parsedResponse);
        } else if (strpos($rawResponse, ';') !== false) {
            //sample: 0;a2a49617-d4f0-4860-887f-04e54bb50f63;OK
            list($responseCode, $transactionId, $responseDescription) = explode(';', $rawResponse);

            $parsedResponse = [
                'ResponseCode' => $responseCode,
                'lowprofilecode' => $transactionId,
                'ResponseDescription' => $responseDescription,
            ];
        }

        return $parsedResponse;
    }

    /**
     * @param InfoInterface $payment
     * @param array $handledResult
     */
    protected function defineGeneralOperationParams(InfoInterface $payment, array $handledResult)
    {
        $payment->setCcLast4($handledResult['ExtShvaParams_CardNumber5']);
        $payment->setCcType($handledResult['ExtShvaParams_Mutag24']);

        $payment->setAdditionalInformation('status_code', $handledResult['ExtShvaParams_Status1']);
        $payment->setAdditionalInformation('jparameter', $handledResult['ExtShvaParams_JParameter29']);

        $payment->setAdditionalInformation('cc_ss_issue', $handledResult['ExtShvaParams_CardTypeCode60']);
        $payment->setAdditionalInformation('cc_mask_last_4', 'xxxx-' . $handledResult['ExtShvaParams_CardNumber5']);
        $payment->setAdditionalInformation('cc_owner_name', $handledResult['CardOwnerName']);
        $payment->setAdditionalInformation('cc_type', $this->config->getCcTypeById($payment->getCcType()));

        $payment->setAdditionalInformation('return_value', $handledResult['ReturnValue']);
    }
}
