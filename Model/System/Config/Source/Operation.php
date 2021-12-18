<?php

namespace SR\Cardcom\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Operation implements ArrayInterface
{
    /**
     * The operations the page needs to perform
     */
    const BILLING = 1;// Billing only
    const BILLING_AND_TOKEN_CREATION = 2;// Billing and creating a token - token is used for recurring billing.
    const TOKEN_CREATION_ONLY = 3;// Only creating a token- use for token creation without billing the credit card.
    const SUSPENDED_DEAL = 4;// suspended deal- allows to suspend the credit card charge and to charge it in the cardcom website interface in a later date.


    /**
     * @inheritdoc
     *
     * NOTICE: unused values are commented
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::BILLING, 'label' => __('Billing (Charge Only)')],
            ['value' => self::BILLING_AND_TOKEN_CREATION, 'label' => __('Billing + Create a Token')],
//            ['value' => self::TOKEN_CREATION_ONLY, 'label' => __('Create a Token (Only)')],
            ['value' => self::SUSPENDED_DEAL, 'label' => __('Suspended Deal')],
        ];
    }

    /**
     * Returns options in "key-value" format
     *
     * NOTICE: unused values are commented
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::BILLING => __('Billing (Charge Only)'),
            self::BILLING_AND_TOKEN_CREATION => __('Billing + Create a Token'),
//            self::TOKEN_CREATION_ONLY => __('Create a Token (Only)'),
           self::SUSPENDED_DEAL => _('Suspended Deal'),
        ];
    }
}
