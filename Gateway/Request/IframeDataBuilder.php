<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Data\Order\OrderAdapter;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Data\Quote\QuoteAdapter;
use Magento\Payment\Gateway\Request\BuilderInterface;
use SR\Cardcom\Gateway\Config\Config;

class IframeDataBuilder implements BuilderInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * IframeDataBuilder constructor.
     * @param Config $config
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Config $config,
        UrlInterface $urlBuilder
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
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

        return [
            'api_endpoint' => 'https://secure.cardcom.co.il/BillGoldLowProfile.aspx',

            'ProductName' => 'Order Id: ' . $this->getUniqueId($order),
            'ReturnValue' => $this->getUniqueId($order),


            //@todo: move into separate Request Builders
            'TerminalNumber' => $this->config->getTerminalNumber($order->getStoreId()),
            'UserName' => $this->config->getApiUsername($order->getStoreId()),
            'CodePage' => '65001',
            'Language' => 'en',//he - Hebrew, en - English, ...
            'APILevel' => '10',// API Level need to be 10

            'SumToBill' => number_format($amount, 2, '.', ''),// Grand Total
            'CoinID' => $this->getCoinID($order),//"1" - NIS, "2" - USD

            'IndicatorUrl' => $this->getIndicatorUrl($order),
            'SuccessRedirectUrl' => $this->urlBuilder->getUrl('softcardcom/checkout/paymentsuccess', ['_secure' => true,]),
            'ErrorRedirectUrl' => $this->urlBuilder->getUrl('softcardcom/checkout/paymenterror', ['_secure' => true,])
        ];
    }

    /**
     * Returns Unique Id of quote/order
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return null|string
     */
    private function getUniqueId(OrderAdapterInterface $orderAdapter)
    {
        if ($orderAdapter instanceof QuoteAdapter) {
            return $orderAdapter->getId() . '-' . rand(9999, 99999);
        } elseif ($orderAdapter instanceof OrderAdapter) {
            return $orderAdapter->getOrderIncrementId();
        }
        return null;
    }

    /**
     * Returns value of CoinId param
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return string
     */
    private function getCoinID(OrderAdapterInterface $orderAdapter)
    {
        if (!$currencyCode = $orderAdapter->getCurrencyCode()) {
            $currencyCode = 'NIS';
        }

        $coinId = ['NIS' => 1, 'ILS' => 1, 'USD' => 2, 'GBP' => 826, 'EUR' => 978, 'AUD' => 36];
        return (string) (isset($coinId[$currencyCode]) ? $coinId[$currencyCode] : $coinId['NIS']);
    }

    /**
     * Returns value of IndicatorUrl param
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return string
     */
    private function getIndicatorUrl(OrderAdapterInterface $orderAdapter)
    {
        $url = $this->urlBuilder->getUrl('softcardcom/checkout/paymentnotify', ['_secure' => true,]);
        $parameter = $this->getUniqueId($orderAdapter);
        return $url . (!empty($parameter) ? '?oid=' . $parameter : '');
    }
}
