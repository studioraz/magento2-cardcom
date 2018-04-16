<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Gateway\Config\Config;

class IframeSourceUrlHandler extends HandlerAbstract
{
    const KEY_IFRAME_SOURCE_URL = 'iframe_source_url';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var EncoderInterface
     */
    private $urlEncoder;

    /**
     * @var string
     */
    private $apiEndpointUrl = 'https://secure.cardcom.co.il/external/LowProfileClearing2.aspx';

    /**
     * IframeSourceUrlHandler constructor.
     * @param Config $config
     * @param UrlInterface $urlBuilder
     * @param EncoderInterface $urlEncoder
     */
    public function __construct(
        Config $config,
        UrlInterface $urlBuilder,
        EncoderInterface $urlEncoder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->urlEncoder = $urlEncoder;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $amount = $handlingSubject['amount'];

        /** @var InfoInterface $payment */
        $payment = $paymentDO->getPayment();

        /** @var OrderAdapterInterface $order */
        $order = $paymentDO->getOrder();

        if (!$handledResult = $this->parseResponse($response)) {
            return;
        }

        $url = $this->apiEndpointUrl;
        $url .= '?terminalnumber=' . $this->config->getTerminalNumber($order->getStoreId());
        $url .= '&rspcode=';
        $url .= '&lowprofilecode=' . $handledResult['lowprofilecode'];

        $payment->setAdditionalInformation(self::KEY_IFRAME_SOURCE_URL, $this->urlEncoder->encode($url));
    }
}
