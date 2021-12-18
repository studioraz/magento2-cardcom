<?php
namespace SR\Cardcom\Event\EmailStopper\Order;

class Email implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        try{
            $order = $observer->getEvent()->getOrder();
            $this->_current_order = $order;

            $payment = $order->getPayment()->getMethodInstance()->getCode();
            if($payment == 'cardcom') {
                $this->stopNewOrderEmail($order);
            }
        }
        catch (\ErrorException $ee){

        }
        catch (\Exception $ex)
        {

        }
        catch (\Error $error){

        }

    }

    public function stopNewOrderEmail(\Magento\Sales\Model\Order $order){
        $order->setCanSendNewEmailFlag(false);
        $order->setSendEmail(false);
        try{
            $order->save();
        }
        catch (\ErrorException $ee){

        }
        catch (\Exception $ex)
        {

        }
        catch (\Error $error){

        }
    }
}