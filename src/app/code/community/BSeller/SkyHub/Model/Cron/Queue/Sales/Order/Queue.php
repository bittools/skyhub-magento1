<?php
/**
 * BSeller Platform | B2W - Companhia Digital
 *
 * Do not edit this file if you want to update this module for future new versions.
 *
 * @category  BSeller
 * @package   BSeller_SkyHub
 *
 * @copyright Copyright (c) 2018 B2W Digital - BSeller Platform. (http://www.bseller.com.br)
 *
 * @author    Tiago Sampaio <tiago.sampaio@e-smart.com.br>
 */

class BSeller_SkyHub_Model_Cron_Queue_Sales_Order_Queue extends BSeller_SkyHub_Model_Cron_Queue_Sales_Abstract
{

    /**
     * Import next orders from the queue in SkyHub.
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function execute(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }

        $limit = 20;
        $count = 0;

        while ($count < $limit) {
            /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
            //$orderData = $this->orderQueueIntegrator()->nextOrder();
            $orderData = $this->orderQueueIntegrator()
                ->nextOrder();

            if (empty($orderData)) {
                $schedule->setMessages($this->__('No order found in the queue.'));
                break;
            }

            try {
                /** @var Mage_Sales_Model_Order $order */
                $order = $this->salesOrderProcessor()->createOrder($orderData);
            } catch (Exception $e) {
                /** The log is already created in the createOrder method. */
                continue;
            }

            if (!$order || !$order->getId()) {
                $schedule->setMessages($this->__('Order cannot be created.'));
                return;
            }

            $message  = $schedule->getMessages();
            $message .= $this->__('Order %s successfully created.', $order->getIncrementId());

            /** @var \SkyHub\Api\Handler\Response\HandlerDefault $isDeleted */
            $isDeleted = $this->orderQueueIntegrator()->deleteByOrder($order);
            
            if ($isDeleted) {
                $message .= ' ' . $this->__('It was also removed from queue.');
            }

            $schedule->setMessages($message);
            $count++;
        }
    }

    
    /**
     * @param Mage_Cron_Model_Schedule $schedule
     *
     * @return bool
     */
    protected function canRun(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->getCronConfig()->salesOrderQueue()->isEnabled()) {
            $schedule->setMessages($this->__('Sales Order Queue Cron is Disabled'));
            return false;
        }
        
        return parent::canRun($schedule);
    }
}
