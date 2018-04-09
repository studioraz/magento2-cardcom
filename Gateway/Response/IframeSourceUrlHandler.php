<?php

namespace SR\Cardcom\Gateway\Response;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Gateway\Config\Config;

class IframeSourceUrlHandler implements HandlerInterface
{
    const KEY_IFRAME_SOURCE_URL = 'iframe_source_url';

    /**
     * @var string
     */
    private $apiEndpointUrl = 'https://secure.cardcom.co.il/external/LowProfileClearing2.aspx';

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
    public function handle(array $handlingSubject, array $response)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $handlingSubject['payment'];
        $amount = $handlingSubject['amount'];

        /** @var InfoInterface $payment */
        $payment = $paymentDO->getPayment();

        /** @var OrderAdapterInterface $order */
        $order = $paymentDO->getOrder();

        $rawResponse = isset($response['object']) ? $response['object'] : '';

        if (!is_array($rawResponse)) {
            //sample: 0;a2a49617-d4f0-4860-887f-04e54bb50f63;OK
            $explodedResponse = explode(';', $rawResponse);

            $handledResult = [
                'ResponseCode' => $explodedResponse[0],
                'Description' => $explodedResponse[2],
                'LowProfileCode' => $explodedResponse[1],
            ];

            $url = $this->apiEndpointUrl;
            $url .= '?terminalnumber=' . $this->config->getTerminalNumber($order->getStoreId());
            $url .= '&rspcode=';
            $url .= '&lowprofilecode=' . $explodedResponse[1];

            //@todo: add one more parameters into Additional Information
            $payment->setAdditionalInformation(self::KEY_IFRAME_SOURCE_URL, $url);
        }
    }
}
