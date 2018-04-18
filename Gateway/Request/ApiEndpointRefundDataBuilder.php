<?php

namespace SR\Cardcom\Gateway\Request;

class ApiEndpointRefundDataBuilder extends DataBuilderAbstract
{
    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::API_ENDPOINT => 'https://secure.cardcom.solutions/interface/ChargeToken.aspx',
        ];
    }
}
