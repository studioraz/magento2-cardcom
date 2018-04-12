<?php

namespace SR\Cardcom\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use SR\Cardcom\Gateway\Config\Config;

/**
 * Class ConfigProvider
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'cardcom';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * ConfigProvider constructor.
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
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'mode' => $this->config->geMode(),
                    'urls' => [
                        'dedicatedPaymentStep' => $this->urlBuilder->getUrl('cardcom/checkout/paymentstep', ['_secure' => true,])
                    ],
                    'customerCcTokenList' => [],
                    'image' => '',
                    'isTokenizationActive' => 0,
                ],
            ],
        ];
    }
}
