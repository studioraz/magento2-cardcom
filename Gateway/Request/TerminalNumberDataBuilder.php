<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class TerminalNumberDataBuilder extends DataBuilderAbstract
{
    /**
     *
     */
    const TERMINAL_NUMBER = 'TerminalNumber';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];

        /** @var OrderAdapterInterface $order */
        $order = $paymentDO->getOrder();

        return [
            self::TERMINAL_NUMBER => $this->config->getTerminalNumber($order->getStoreId()),
        ];
    }
}
