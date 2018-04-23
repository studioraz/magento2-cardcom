<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

class LanguageDataBuilder extends DataBuilderAbstract
{
    const LANGUAGE = 'Language';

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
            self::LANGUAGE => $this->config->getLanguageCode($order->getStoreId()),
        ];
    }
}
