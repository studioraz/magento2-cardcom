<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\Order\OrderAdapter;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\Quote\QuoteAdapter;
use Magento\Payment\Gateway\Request\BuilderInterface;
use SR\Cardcom\Gateway\Config\Config;

abstract class DataBuilderAbstract implements BuilderInterface
{
    /**
     * API endpoint url
     */
    const API_ENDPOINT = 'api_endpoint';

    /**
     * @var Config
     */
    protected $config;

    /**
     * DataBuilderAbstract constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Returns value of CoinId param
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return string
     */
    protected function getCoinID(OrderAdapterInterface $orderAdapter)
    {
        if (!$currencyCode = $orderAdapter->getCurrencyCode()) {
            $currencyCode = 'NIS';
        }

        $coinId = ['NIS' => 1, 'ILS' => 1, 'USD' => 2, 'GBP' => 826, 'EUR' => 978, 'AUD' => 36];
        return (string) (isset($coinId[$currencyCode]) ? $coinId[$currencyCode] : $coinId['NIS']);
    }

    /**
     * Returns Unique Id of quote/order
     *
     * @param OrderAdapterInterface $orderAdapter
     * @return null|string
     */
    protected function getUniqueId(OrderAdapterInterface $orderAdapter)
    {
        if ($orderAdapter instanceof QuoteAdapter) {
            return $orderAdapter->getId() . '-' . rand(9999, 99999);
        } elseif ($orderAdapter instanceof OrderAdapter) {
            return $orderAdapter->getOrderIncrementId() . '-' . rand(9999, 99999);
        }
        return null;
    }
}
