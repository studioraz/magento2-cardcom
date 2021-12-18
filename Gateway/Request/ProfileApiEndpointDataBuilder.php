<?php

namespace SR\Cardcom\Gateway\Request;

class ProfileApiEndpointDataBuilder extends DataBuilderAbstract
{
    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::API_ENDPOINT => 'https://secure.cardcom.solutions/BillGoldLowProfile.aspx',
        ];
    }
}
