<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Quote\Api\Data\PaymentInterface as QuotePaymentInterface;
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
     * StoreConfigBuilder constructor.
     * @param Config $config
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
        /** @var InfoInterface $payment */
        $payment = $buildSubject['payment'];
        $amount = $buildSubject['amount'];

        /** @var Quote $quote */
        $quote = $payment->getQuote();

        return [
            'api_endpoint' => 'https://secure.cardcom.co.il/BillGoldLowProfile.aspx',

            'ProductName' => 'Order Id: ' . $this->getUniqueId($buildSubject['payment']),
            'ReturnValue' => $this->getUniqueId($buildSubject['payment']),


            //@todo: move into separate Request Builders
            'TerminalNumber' => $this->config->getTerminalNumber($quote->getStoreId()),
            'UserName' => $this->config->getApiUsername($quote->getStoreId()),
            'CodePage' => '65001',
            'Language' => 'en',//he - Hebrew, en - English, ...
            'APILevel' => '10',// API Level need to be 10

            'SumToBill' => number_format($amount, 2, '.', ''),// Grand Total
            'CoinID' => $this->getCoinID($payment),//1 - NIS, 2 - USD

            'IndicatorUrl' => $this->getIndicatorUrl($payment),
            'SuccessRedirectUrl' => $this->urlBuilder->getUrl('softcardcom/checkout/paymentsuccess', ['_secure' => true,]),
            'ErrorRedirectUrl' => $this->urlBuilder->getUrl('softcardcom/checkout/paymenterror', ['_secure' => true,])
        ];
    }

    /**
     * Returns Unique Id of quote/order
     *
     * @param InfoInterface $payment
     * @return null|string
     */
    private function getUniqueId(InfoInterface $payment)
    {
        if ($payment instanceof QuotePaymentInterface) {
            return $payment->getQuote()->getId() . '-' . rand(9999, 99999);
        } elseif ($payment instanceof OrderPaymentInterface) {
            return $payment->getOrder()->getIncrementId();
        }
        return null;
    }

    /**
     * Returns value of CoinId param
     *
     * @param InfoInterface $payment
     * @return int
     */
    private function getCoinID(InfoInterface $payment)
    {
        $currencyCode = 'NIS';
        if ($payment instanceof QuotePaymentInterface) {
            $currencyCode = $payment->getQuote()->getQuoteCurrencyCode();
        } elseif ($payment instanceof OrderPaymentInterface) {
            $currencyCode = $payment->getOrder()->getOrderCurrencyCode();
        }

        $coinId = ['NIS' => 1, 'ILS' => 1, 'USD' => 2, 'GBP' => 826, 'EUR' => 978, 'AUD' => 36];
        return isset($coinId[$currencyCode]) ? $coinId[$currencyCode] : $coinId['NIS'];
    }

    /**
     * Returns value of IndicatorUrl param
     *
     * @param InfoInterface $payment
     * @return string
     */
    private function getIndicatorUrl(InfoInterface $payment)
    {
        $url = $this->urlBuilder->getUrl('softcardcom/checkout/paymentnotify', ['_secure' => true,]);
        $parameter = $this->getUniqueId($payment);
        return $url . (!empty($parameter) ? '?oid=' . $parameter : '');
    }
}
