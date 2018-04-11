<?php

namespace SR\Cardcom\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class FetchTransactionInfoResponseValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $isValid = false;
        $errorMessages = [];

        $rawResponse = isset($validationSubject['response']['object']) ? $validationSubject['response']['object'] : '';

        if (strpos($rawResponse, '=') !== false) {
            //sample: ResponseCode=0&Description=Low Profile Code Found&terminalnumber=1000&lowprofilecode=7a1735e1-e818-48b4-a1ba-491094355055&Operation=4&ProssesEndOK=0&CardValidityYear=2020&CardValidityMonth=10&CardOwnerID=040617649&NumOfPayments=1&SuspendedDealResponseCode=0&SuspendedDealId=2338&SuspendedDealGroup=1&CallIndicatorResponse=The+remote+name+could+not+be+resolved%3a+%27sr.m19x.de&ReturnValue=145000027&CoinId=2&OperationResponse=0&OperationResponseText=OK
            parse_str($rawResponse, $explodedResponse);

            if (isset($explodedResponse['OperationResponseText']) && $explodedResponse['OperationResponseText'] === 'OK') {
                $isValid = true;
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
