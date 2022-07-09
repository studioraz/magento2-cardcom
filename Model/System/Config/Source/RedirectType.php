<?php

namespace SR\Cardcom\Model\System\Config\Source;
use \Magento\Framework\Data\OptionSourceInterface;
use function React\Promise\map;

class RedirectType implements OptionSourceInterface
{

    const REDIRECT_IFRAME = 1;
    const REDIRECT_STANDALONE_PAGE = 2;

    /**
     * @return array[]
     */
    public function toOptionArray() : array
    {
        return [
            ['value' => self::REDIRECT_IFRAME, 'label' => __('Custom Page with Iframe')],
            ['value' => self::REDIRECT_STANDALONE_PAGE, 'label' => __('Cardcom Standalone Payment Page')],
        ];
    }

    /**
     * @return array[]
     */
    public function toArray() : array
    {
        return array_map(function($option) {
            return [
                $option['value'] => $option['label']
            ];
        }, $this->toOptionArray());

    }
}
