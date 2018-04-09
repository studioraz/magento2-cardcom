<?php

namespace SR\Cardcom\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class InitializeIframeResponseValidator extends AbstractValidator
{
    /**
     * @inheritdoc
     */
    public function validate(array $validationSubject)
    {
        $isValid = false;
        $errorMessages = [];

        $rawResponse = isset($validationSubject['response']['object']) ? $validationSubject['response']['object'] : '';

        if (strpos($rawResponse, ';') !== false) {
            //sample: 0;a2a49617-d4f0-4860-887f-04e54bb50f63;OK
            $explodedResponse = explode(';', $rawResponse);

            if (count($explodedResponse) > 2 && $explodedResponse[2] === 'OK') {
                $isValid = true;
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }
}
