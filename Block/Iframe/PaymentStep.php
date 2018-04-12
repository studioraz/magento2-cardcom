<?php

namespace SR\Cardcom\Block\Iframe;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;

class PaymentStep extends Template
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * PaymentStep constructor.
     * @param Template\Context $context
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Returns source url for iFrame element
     *
     * @return string|null
     */
    public function getIframeSourceUrl()
    {
        return $this->coreRegistry->registry('cardcom_iframe_source_url');
    }
}
