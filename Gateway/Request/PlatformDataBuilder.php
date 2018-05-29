<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use SR\Cardcom\Gateway\Config\Config;

class PlatformDataBuilder extends DataBuilderAbstract
{
    /**
     * Param in order to pass Magento version
     *
     * Format: {string} 'Mage-{version}'
     */
    const PLUGIN = 'Plugin';

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * PlatformDataBuilder constructor.
     * @param Config $config
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        Config $config,
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;

        parent::__construct($config);
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
            self::PLUGIN => 'Mage-' . $this->productMetadata->getVersion(),
        ];
    }
}
