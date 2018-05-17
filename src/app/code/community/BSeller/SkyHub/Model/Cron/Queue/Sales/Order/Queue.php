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
    
    public function execute(Mage_Cron_Model_Schedule $schedule)
    {
        $this->processStoreIteration($this, 'executeIntegration', $schedule);
    }
    

    /**
     * Import next orders from the queue in SkyHub.
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function executeIntegration(Mage_Cron_Model_Schedule $schedule, Mage_Core_Model_Store $store)
    {
        if (!$this->canRun($schedule, $store->getId())) {
            return;
        }

        $limit = $this->getCronConfig()->salesOrderQueue()->getLimit();
        $count = 0;

        while ($count < $limit) {
            /** @var \SkyHub\Api\Handler\Response\HandlerDefault $result */
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
                $schedule->setMessages($this->__('Order cannot be created in store %s.', $store->getName()));
                return;
            }

            $message  = $schedule->getMessages();
            $message .= $this->__(
                'Order %s successfully created in store %s.', $order->getIncrementId(), $store->getName()
            );

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
     * @param int|null                 $storeId
     *
     * @return bool
     */
    protected function canRun(Mage_Cron_Model_Schedule $schedule, $storeId = null)
    {
        if (!$this->getCronConfig()->salesOrderQueue()->isEnabled($storeId)) {
            $schedule->setMessages($this->__('Sales Order Queue Cron is Disabled'));
            return false;
        }
        
        return parent::canRun($schedule, $storeId);
    }
}
