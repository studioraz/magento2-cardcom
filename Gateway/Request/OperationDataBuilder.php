<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use SR\Cardcom\Gateway\Config\Config;

class OperationDataBuilder implements BuilderInterface
{
    /**
     *
     */
    const OPERATION = 'Operation';

    /**
     * @var Config
     */
    private $config;

    /**
     * StoreConfigBuilder constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
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
            self::OPERATION => $this->config->getOperationId($order->getStoreId()),
        ];
    }
}
