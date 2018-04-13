<?php

namespace SR\Cardcom\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class LanguageCode implements ArrayInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'en', 'label' => __('English')],
            ['value' => 'he', 'label' => __('Hebrew')],
        ];
    }

    /**
     * Returns options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'en' => __('English'),
            'he' => __('Hebrew')
        ];
    }
}
