<?php

namespace SR\Cardcom\Model;

use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Command\CommandManagerInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Validator\ValidatorPoolInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Payment\Model\Method\Adapter;
use Psr\Log\LoggerInterface;

use Magento\Payment\Gateway\Command\ResultInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Payment\Gateway\Command\CommandException;

/**
 * Payment method facade.
 *
 * @see Magento_Braintree as a sample
 *
 * Class CardcomFacade
 * @package SR\Cardcom\Model
 */
class CardcomFacade extends Adapter
{
    /**
     * @var PaymentDataObjectFactory
     */
    private $paymentDataObjectFactory;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * CardcomFacade constructor.
     * @param ManagerInterface $eventManager
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param string $code
     * @param string $formBlockType
     * @param string $infoBlockType
     * @param CommandPoolInterface|null $commandPool
     * @param ValidatorPoolInterface|null $validatorPool
     * @param CommandManagerInterface|null $commandExecutor
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ManagerInterface $eventManager,
        ValueHandlerPoolInterface $valueHandlerPool,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        string $code,
        string $formBlockType,
        string $infoBlockType,
        CommandPoolInterface $commandPool = null,
        ValidatorPoolInterface $validatorPool = null,
        CommandManagerInterface $commandExecutor = null,
        LoggerInterface $logger = null
    ) {
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->commandPool = $commandPool;

        parent::__construct($eventManager, $valueHandlerPool, $paymentDataObjectFactory, $code, $formBlockType, $infoBlockType, $commandPool, $validatorPool, $commandExecutor, $logger);
    }

    /**
     * @param InfoInterface $payment
     * @param $amount
     * @return ResultInterface|null
     * @throws NotFoundException
     * @throws CommandException
     */
    public function initializeIframe(InfoInterface $payment, $amount)
    {
        $commandCode = 'initialize_iframe';
        $arguments = ['payment' => $payment, 'amount' => $amount];

        /** @var InfoInterface|null $payment */
        $payment = null;
        if (isset($arguments['payment']) && $arguments['payment'] instanceof InfoInterface) {
            $payment = $arguments['payment'];
            $arguments['payment'] = $this->paymentDataObjectFactory->create($payment);
        }

        $command = $this->commandPool->get($commandCode);
        return $command->execute($arguments);
    }
}
