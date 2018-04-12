<?php

namespace SR\Cardcom\Block\Iframe;

use Magento\Framework\View\Element\Template;

class Error extends Template
{
    /**
     * @return mixed
     */
    public function getErrorCode()
    {
        return $this->_request->getParam('ErrorCode', 100);
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->_request->getParam('ErrorText', 'Error occurs');
    }
}
