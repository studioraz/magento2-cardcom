<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use SR\Cardcom\Gateway\Config\Config;

class TerminalNumberDataBuilder implements BuilderInterface
{
    /**
     *
     */
    const TERMINAL_NUMBER = 'TerminalNumber';

    /**
     * @var Config
     */
    private $config;

    /**
     * ProfileApiEndpointDataBuilder constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

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
