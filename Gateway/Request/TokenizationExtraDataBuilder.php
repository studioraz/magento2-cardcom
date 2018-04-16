<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use SR\Cardcom\Gateway\Config\Config;
use SR\Cardcom\Model\System\Config\Source\Operation;

class TokenizationExtraDataBuilder extends DataBuilderAbstract
{
    /**
     * When the token and the card number should be removed from Cardcom system
     *
     * Mandatory: No
     * Format: string "DD/MM/YYYY"
     */
    const TOKEN_DELETE_DATE = 'CreateTokenDeleteDate';

    /**
     * Type of test to be performed on the card
     * *    J2- Testing only told criticism of the card
     * *    J5 - Test and booking of the transferred amount. you will recive One-time confirmation code. The confirmation code need to be transpfer to cardcom systems when billing should be done.
     *           Using J5 subject to approval of credit card company. Parameter obtained approval will be show in Indicator ( Notify)  URL : TokenApprovalNumber
     *
     * Mandatory: No
     * Format: string "2" [for J2 -> 2, for J5 -> 5]
     */
    const TOKEN_VALIDATE_TYPE = 'CreateTokenJValidateType';

    /**
     * Token expire time, in seconds
     */
    const TOKET_EXPIRE_TIME = 31104000;// 1 year = 60 * 60 * 24 * 30 * 12

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * TokenizationExtraDataBuilder constructor.
     * @param Config $config
     * @param TimezoneInterface $timezone
     */
    public function __construct(Config $config, TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
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

        // Such Data builder should add extra fields only if Operation is Operation::BILLING_AND_TOKEN_CREATION orOperation::TOKEN_CREATION_ONLY
        if (!in_array($this->config->getOperationId($order->getStoreId()), [Operation::BILLING_AND_TOKEN_CREATION, Operation::TOKEN_CREATION_ONLY])) {
            return [];
        }

        return [
            self::TOKEN_DELETE_DATE => $this->getFormattedTokenExpireDate(),
            self::TOKEN_VALIDATE_TYPE => 2, // see related constant comment
        ];
    }

    /**
     * Returns Formatted datetime of Token Expire
     *
     * @return string
     */
    private function getFormattedTokenExpireDate()
    {
        $expireTime = time() + self::TOKET_EXPIRE_TIME;
        return $this->timezone->date($expireTime) ->format('d/m/Y');
    }
}
