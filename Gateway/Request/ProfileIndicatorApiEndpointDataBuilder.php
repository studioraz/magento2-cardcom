<?php

namespace SR\Cardcom\Gateway\Request;

class ProfileIndicatorApiEndpointDataBuilder extends DataBuilderAbstract
{
    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        return [
            self::API_ENDPOINT => 'https://secure.cardcom.co.il/Interface/BillGoldGetLowProfileIndicator.aspx',
        ];
    }
}
