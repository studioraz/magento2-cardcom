<?php

namespace SR\Cardcom\Model\Checkout;

use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use SR\Cardcom\Model\Ui\ConfigProvider;

class PaymentInformationManagementPlugin
{
    /**
     * Around Plugin
     *
     * We need to proceed checkout flow with CUSTOM PAYMENT step
     * so we skip Order Placing process if CardomPaymentMethod is used
     *
     * both Guest and User flows are handled
     *
     * @origin GuestPaymentInformationManagementInterface::savePaymentInformationAndPlaceOrder
     * @origin PaymentInformationManagementInterface::savePaymentInformationAndPlaceOrder
     *
     * @param GuestPaymentInformationManagementInterface|PaymentInformationManagementInterface $subject
     * @param Callable $proceed
     * @param mixed ...$arguments
     * @return bool
     */
    public function aroundSavePaymentInformationAndPlaceOrder($subject, $proceed, ...$arguments)
    {
        $paymentMethod = null;
        for($i=0; $i < count($arguments); $i++) {
            if ($arguments[$i] instanceof PaymentInterface) {
                $paymentMethod = $arguments[$i];
                break;
            }
        }

        if (!$paymentMethod || $paymentMethod->getMethod() !== ConfigProvider::CODE) {
            return $proceed(...$arguments);
        }

        if ($subject instanceof GuestPaymentInformationManagementInterface) {
            list($cartId, $email, $paymentMethod, $billingAddress) = $arguments;
            $subject->savePaymentInformation($cartId, $email, $paymentMethod, $billingAddress);
        } elseif ($subject instanceof PaymentInformationManagementInterface) {
            list($cartId, $paymentMethod, $billingAddress) = $arguments;
            $subject->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
        }

        return true;
    }
}
