<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use SR\Cardcom\Gateway\Config\Config;

class IframeDataBuilder extends DataBuilderAbstract
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * IframeDataBuilder constructor.
     * @param Config $config
     * @param UrlInterface $urlBuilder
     */
    public function __construct(Config $config, UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $amount = $buildSubject['amount'];

        /** @var OrderAdapterInterface $order */
        $order = $paymentDO->getOrder();

        /** @var AddressAdapterInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();

        return [
            'ProductName' => $billingAddress->getEmail(),
            'ReturnValue' => $this->getUniqueId($order),

            //@todo: move into separate Request Builders
            'CodePage' => '65001',
            'APILevel' => '10',// API Level need to be 10

            'SumToBill' => number_format($amount, 2, '.', ''),// Grand Total
            'CoinID' => $this->getCoinID($order),//"1" - NIS, "2" - USD

            'IndicatorUrl' => $this->getIndicatorUrl($order),
            'SuccessRedirectUrl' => $this->urlBuilder->getUrl('cardcom/checkout/paymentsuccess', ['_secure' => true,]),
            'ErrorRedirectUrl' => $this->urlBuilder->getUrl('cardcom/checkout/paymenterror', ['_secure' => true,])
        ];
    }

    /**
     * Returns value of IndicatorUrl param
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return string
     */
    private function getIndicatorUrl(OrderAdapterInterface $orderAdapter)
    {
        $url = $this->urlBuilder->getUrl('cardcom/checkout/paymentnotify', ['_secure' => true,]);
        $parameter = $this->getUniqueId($orderAdapter);
        return $url . (!empty($parameter) ? '?oid=' . $parameter : '');
    }
}
