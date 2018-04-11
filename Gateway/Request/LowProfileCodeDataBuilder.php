<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class LowProfileCodeDataBuilder implements BuilderInterface
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
