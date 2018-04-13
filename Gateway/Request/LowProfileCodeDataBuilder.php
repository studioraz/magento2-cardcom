<?php

namespace SR\Cardcom\Gateway\Request;

class LowProfileCodeDataBuilder extends DataBuilderAbstract
{
    /**
     *
     */
    const TRANSACTION_ID = 'lowprofilecode';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $transactionId = $buildSubject['transactionId'];

        return [
            self::TRANSACTION_ID => $transactionId,
        ];
    }
}
