<?php

namespace SR\Cardcom\Gateway\Request;

use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Model\InfoInterface;
use SR\Cardcom\Model\System\Config\Source\Operation;

class TokenDataBuilder extends DataBuilderAbstract
{
    /**
     * The chargeable token
     *
     * Mandatory: Yes
     * Format: string "aa-bb-cc"
     */
    const TOKEN = 'TokenToCharge.Token';

    /**
     * Salt token as transferred during creation. is not used ))
     *
     * Mandatory: No
     * Format: string "1234"
     */
    const SALT = 'TokenToCharge.Salt';

    /**
     * MM card validity
     *
     * Mandatory: Yes
     * Format: string "03"
     */
    const CARD_VALIDITY_MONTH = 'TokenToCharge.CardValidityMonth';

    /**
     * YY card validity
     *
     * Mandatory: Yes
     * Format: string "19"
     */
    const CARD_VALIDITY_YEAR = 'TokenToCharge.CardValidityYear';

    /**
     * Billing amount
     *
     * Mandatory: Yes
     * Format: string "1100.10"
     */
    const SUM_TO_BILL = 'TokenToCharge.SumToBill';

    /**
     * Possible values:
     *
     * 1 - If the terminal has a CVV, it is not possible to charge a transaction token without passing this parameter
     * 2 - Confirmation number received when performing J5 to the card. Use the confirmation number only once.
     * 3 - If the console does not have a CVV it is not possible to pass this parameter;
     * 4 - Customers who are removed with IMAPAY should not pass this parameter. If the money is transferred, it will be received on he 15th of next month.
     *
     * Mandatory: Yes
     * Format: string "0"
     */
    const APPROVAL_NUMBER = 'TokenToCharge.ApprovalNumber';

    /**
     * ID. The owner of the card.
     *
     * Mandatory: Yes
     * Format: string "040617644"
     */
    const IDENTITY_NUMBER = 'TokenToCharge.IdentityNumber';

    /**
     * Billable currency
     *
     * Mandatory: No
     * Format: "1" Values: [1 - NIS, 2 - USD, ....]
     */
    const COIN_ID = 'TokenToCharge.CoinID';

    /**
     * Amount for star billing
     *
     * Mandatory: No
     * Format: "0"
     */
    const SUM_IN_STAR = 'TokenToCharge.SumInStars';

    /**
     * Multiple billing payments
     *
     * Mandatory: No
     * Format: "1"
     */
    const NUM_OF_PAYMENTS = 'TokenToCharge.NumOfPayments';

    /**
     * Whether to credit a token instead of a charge
     *
     * Mandatory: No
     * Format: "false"
     * Values: true - refund, false - charge
     */
    const REFUND_INSTEAD_OF_CHARGE = 'TokenToCharge.RefundInsteadOfCharge';

    /**
     * If credits are issued with a token , the API password must be passed to that user's credits
     *
     * Mandatory: No
     * Format: "aSD34SDF-1asd"
     */
    const USER_PASSWORD = 'TokenToCharge.UserPassword';

    /**
     * not in use
     */
    const EXTENDED_PARAMETERS = 'TokenToCharge.ExtendedParameters';

    /**
     * The name of the card holder to display in reports
     *
     * Mandatory: No
     * Format: "0000000"
     */
    const CARD_OWNER_NAME = 'TokenToCharge.CardOwnerName';

    /**
     * Customer number in the work of a beneficiary rabbi
     *
     * Mandatory: No
     * Format: "1234"
     */
    const SAPAK_MUTAV = 'TokenToCharge.SapakMutav';

    /**
     * [OPTIONAL] Which company has the positive charge
     *
     * Mandatory: No
     */
    const TOKEN_COMPANY_USER_NAME = 'TokenToCharge.TokenCompanyUserName';

    /**
     * [OPTIONAL] Which company has the positive charge
     *
     * Mandatory: No
     */
    const TOKEN_COMPANY_PASSWORD = 'TokenToCharge.TokenCompanyPassword';

    /**
     * [OPTIONAL] First payment amount in heart payments. Amounts are in agorot!
     * Note: The following test should be performed:  Billable amount = equal to = First payment + Fixed payment * (Number of payments -1)
     *
     * Mandatory: No
     */
    const FIRST_PAYMENT_SUM_AGOROT = 'TokenToCharge.FirstPaymentSumAgorot';

    /**
     * [OPTIONAL] the amount of other payments to the heart/ Amounts are in agorot!
     *
     * Mandatory: No
     */
    const CONST_PAYMENT_AGOROT = 'TokenToCharge.ConstPaymentAgorot';

    /**
     * [OPTIONAL] Token that exists in the system and want to perform a frame capture again
     * You can pass this parameter with a value of "5"
     *
     * Mandatory: No
     * Values: 5 - Frame Perception, 2 - Card Test
     */
    const JPARAMETER = 'TokenToCharge.JParameter';

    /**
     * [PREFERRED] Unique Transaction ID
     * The transaction ID must be transferred to your system. If we receive the same system
     * identifier, we return a double transaction. And will not charge the transaction 25 characters long
     *
     * Mandatory: No
     */
    const UNIQ_ASMACHTA = 'UniqAsmachta';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        /** @var PaymentDataObjectInterface $paymentDO */
        $paymentDO = $buildSubject['payment'];
        $amount = $buildSubject['amount'];

        /** @var InfoInterface $payment */
        $payment = $paymentDO->getPayment();

        /** @var OrderAdapterInterface $order */
        $order = $paymentDO->getOrder();

        // Token Operations only can be performed
        if (!in_array($this->config->getOperationId($order->getStoreId()), [Operation::BILLING_AND_TOKEN_CREATION, Operation::TOKEN_CREATION_ONLY])) {
            return [];
        }

        return [
            //@todo: move into separate Request Builders
            'CodePage' => '65001',
            'Language' => 'en',//he - Hebrew, en - English, ...
            'APILevel' => '10',// API Level need to be 10

            self::USER_PASSWORD => $this->config->getApiPassword($order->getStoreId()),

            self::TOKEN => $payment->getAdditionalInformation('token'),
            self::CARD_VALIDITY_MONTH => $payment->getCcExpMonth(),
            self::CARD_VALIDITY_YEAR => substr($payment->getCcExpYear(), -2),
            self::SUM_TO_BILL => number_format($amount, 2, '.', ''),
            self::APPROVAL_NUMBER => $payment->getAdditionalInformation('approval_number'),
            self::IDENTITY_NUMBER => $payment->getAdditionalInformation('cc_owner_id'),
            self::COIN_ID => $this->getCoinID($order),//"1" - NIS, "2" - USD
            self::SUM_IN_STAR => '0',

            //@todo: implement logic for this param when it is needed
            self::NUM_OF_PAYMENTS => '1',

            self::REFUND_INSTEAD_OF_CHARGE => 'true',//tmp value ONLY FOR REFUND OPERATION
            self::CARD_OWNER_NAME => $payment->getAdditionalInformation('cc_owner_name'),
           //self::JPARAMETER => '5',//tmp value
            self::UNIQ_ASMACHTA => rand(9999, 99999),
        ];
    }
}
