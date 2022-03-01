<?php

namespace SR\Cardcom\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;
use Magento\Framework\App\Area;

class Info extends ConfigurableInfo
{
    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field): Phrase
    {
        return __($field);
    }

    /**
     * Check whether payment information should show up in secure mode
     * true => only "public" payment information may be shown
     * false => full information may be shown
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsSecureMode(): bool
    {

        if ($this->getArea() == Area::AREA_ADMINHTML) {
            return false;
        }

        return parent::getIsSecureMode();
    }
}
