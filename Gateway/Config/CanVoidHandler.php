<?php

namespace SR\Cardcom\Gateway\Config;

use Magento\Payment\Gateway\Config\ValueHandlerInterface;

class CanVoidHandler implements ValueHandlerInterface
{
    /**
     * @inheritdoc
     */
    public function handle(array $subject, $storeId = null)
    {
        // TODO: Implement handle() method.
        return false;
    }
}
