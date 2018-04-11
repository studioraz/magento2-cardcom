<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class ProfileApiEndpointDataBuilder implements BuilderInterface
{
    /**
     *
     */
    const API_ENDPOINT = 'api_endpoint';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::API_ENDPOINT => 'https://secure.cardcom.co.il/BillGoldLowProfile.aspx',
        ];
    }
}
