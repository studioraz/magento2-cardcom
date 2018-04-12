<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use SR\Cardcom\Gateway\Config\Config;

class UserNameDataBuilder extends DataBuilderAbstract
{
    /**
     *
     */
    const USER_NAME = 'UserName';

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
            self::USER_NAME => $this->config->getApiUsername($order->getStoreId()),
        ];
    }
}
