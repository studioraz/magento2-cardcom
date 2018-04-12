<?php

namespace SR\Cardcom\Gateway\Request;

class StoreConfigBuilder extends DataBuilderAbstract
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
