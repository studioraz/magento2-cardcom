<?php

namespace SR\Cardcom\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Checkout\Helper\Data as CheckoutHelper;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Type\Onepage as CheckoutTypeOnepage;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Url\DecoderInterface;
use Magento\Payment\Gateway\Command\CommandException;
use Magento\Payment\Helper\Data as PaymentData;
use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use SR\Cardcom\Gateway\Config\Config;
use SR\Cardcom\Gateway\Response\IframeSourceUrlHandler;
use SR\Cardcom\Model\Ui\ConfigProvider;


class Checkout
{
    /**
     * Payment method type
     *
     * @var string
     */
    protected $methodType = ConfigProvider::CODE;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var CheckoutHelper
     */
    private $checkoutData;

    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var OrderResource
     */
    protected $orderResource;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var PaymentData
     */
    private $paymentData;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Quote
     */
    private $quote;

    /**
     * @var MethodInterface
     */
    private $methodInstance;

    /**
     * @var DecoderInterface
     */
    private $urlDecoder;

    /**
     * Checkout constructor.
     * @param DataObjectHelper $dataObjectHelper
     * @param CheckoutHelper $checkoutData
     * @param CheckoutSession $checkoutSession
     * @param CustomerSession $customerSession
     * @param CartManagementInterface $quoteManagement
     * @param CartRepositoryInterface $quoteRepository
     * @param PaymentData $paymentData
     * @param Config $config
     * @param OrderResource $orderResource
     * @param OrderSender $orderSender
     * @param DecoderInterface $urlDecoder
     * @throws \Exception
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        CheckoutHelper $checkoutData,
        CheckoutSession $checkoutSession,
        CustomerSession $customerSession,
        CartManagementInterface $quoteManagement,
        CartRepositoryInterface $quoteRepository,
        PaymentData $paymentData,
        Config $config,
        OrderResource $orderResource,
        OrderSender $orderSender,
        DecoderInterface $urlDecoder
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->checkoutData     = $checkoutData;
        $this->checkoutSession  = $checkoutSession;
        $this->customerSession  = $customerSession;
        $this->quoteManagement  = $quoteManagement;
        $this->quoteRepository  = $quoteRepository;
        $this->paymentData      = $paymentData;
        $this->config           = $config;
        $this->orderResource    = $orderResource;
        $this->orderSender      = $orderSender;
        $this->urlDecoder       = $urlDecoder;

        $this->initQuote();
    }

    /**
     * @return Quote
     * @throws \Exception
     */
    private function initQuote()
    {
        if (is_null($this->quote)) {
            $this->quote = $this->checkoutSession->getQuote();
            if (!$this->quote->getId()) {
                throw new \Exception('Quote instance is required.');
            }
        }

        return $this->quote;
    }


    /**
     * Executes Initialize_Iframe command and return Iframe Source url
     *
     * @return array|mixed|null
     * @throws LocalizedException
     * @throws NotFoundException
     * @throws CommandException
     */
    public function getIframeSourceUrl()
    {
        /** @var CardcomFacade $methodInstance */
        $methodInstance = $this->getMethodInstance();

        $this->quote->collectTotals();

        /** @var Quote\Payment $payment */
        $payment = $this->quote->getPayment();

        $amount = $this->quote->getGrandTotal();

        $methodInstance->initializeIframe($payment, $amount);

        $this->quoteRepository->save($this->quote);

        $url = $payment->getAdditionalInformation(IframeSourceUrlHandler::KEY_IFRAME_SOURCE_URL);
        return $this->urlDecoder->decode($url);
    }

    /**
     * @param string|null $transactionId
     * @return Order|null
     * @throws LocalizedException
     * @throws CouldNotSaveException
     */
    public function placeOrder($transactionId = null)
    {
        // prepare Quote
        $this->quote->setCheckoutMethod($this->getCheckoutMethod());

        // prepare Shipping information
        $shipping = $this->quote->getShippingAddress();

        // prepare Billing information
        $billing = $this->quote->getBillingAddress();

        // start: prepare Payment information
        /** @var CardcomFacade $methodInstance */
        $methodInstance = $this->getMethodInstance();

        /** @var Quote\Payment $payment */
        $payment = $this->quote->getPayment();
        $payment->setMethod(ConfigProvider::CODE);

        // command to fetch and fill transaction information into Payment Object
        $methodInstance->fetchTransactionInfo($payment, $transactionId);
        // end: prepare Payment information

        //save quote
        $this->quote->collectTotals();
        $this->quoteRepository->save($this->quote);

        //create order
        $orderId = $this->quoteManagement->placeOrder($this->quote->getId());

        $order = null;
        if ($orderId) {
            /** @var Order $order */
            $order = $this->checkoutSession->getLastRealOrder();

            $this->placeOrderAfter($order);
        }

        return $order;
    }

    /**
     * Actions which should be run after Order is placed
     *
     * @param Order $order
     * @return $this
     * @throws AlreadyExistsException
     */
    private function placeOrderAfter(Order $order)
    {
        // Notify customer about this order
        if (!$order->getEmailSent()) {
            $comment = 'Notified customer about order\'s payment';

            $this->orderSender->send($order);
            $order->addStatusHistoryComment($comment)
                ->setIsCustomerNotified(true);
        }

        $this->orderResource->save($order);

        $this->checkoutSession
            ->setLastQuoteId($this->quote->getId())
            ->setLastSuccessQuoteId($this->quote->getId())
            ->clearHelperData()
        ;

        // Such params are defined here: \Magento\Quote\Model\QuoteManagement::placeOrder
        // but somehow they don't exist on current step but they should be.
        // so define them again
        $this->checkoutSession->setLastOrderId($order->getId());
        $this->checkoutSession->setLastRealOrderId($order->getIncrementId());
        $this->checkoutSession->setLastOrderStatus($order->getStatus());

        $this->quote->setIsActive(false);
        $this->quoteRepository->save($this->quote);

        return $this;
    }

    /**
     * @return MethodInterface
     * @throws LocalizedException
     */
    private function getMethodInstance()
    {
        if (is_null($this->methodInstance)) {
            $this->methodInstance = $this->paymentData->getMethodInstance($this->methodType);
        }
        return $this->methodInstance;
    }

    /**
     * Returns checkout method
     *
     * @return string
     */
    private function getCheckoutMethod()
    {
        if ($this->customerSession->isLoggedIn()) {
            return CheckoutTypeOnepage::METHOD_CUSTOMER;
        }
        if (!$this->quote->getCheckoutMethod()) {
            if ($this->checkoutData->isAllowedGuestCheckout($this->quote)) {
                $this->quote->setCheckoutMethod(CheckoutTypeOnepage::METHOD_GUEST);
            } else {
                $this->quote->setCheckoutMethod(CheckoutTypeOnepage::METHOD_REGISTER);
            }
        }
        return $this->quote->getCheckoutMethod();
    }
}
