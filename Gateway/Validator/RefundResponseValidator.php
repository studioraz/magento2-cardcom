<?php

namespace SR\Cardcom\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class RefundResponseValidator extends AbstractValidator
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
            //sample: ResponseCode=0&Description=someDescriptionTextInternalDealNumber=26477043&InvoiceResponse.ResponseCode=9998&InvoiceResponse.Description=SomeDescriptiontext&InvoiceResponse.InvoiceNumber=-1&InvoiceResponse.InvoiceType=-1&ApprovalNumber=0012345
            parse_str($rawResponse, $explodedResponse);

            if (isset($explodedResponse['ResponseCode']) && $explodedResponse['ResponseCode'] === '0') {
                $isValid = true;
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
