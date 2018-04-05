<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

class StoreConfigBuilder implements BuilderInterface
{
    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        // TODO: Implement build() method.
        return [
            'store_id' => '1',
        ];
    }
}
