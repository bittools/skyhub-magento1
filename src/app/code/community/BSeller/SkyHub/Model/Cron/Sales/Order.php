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
 
class BSeller_SkyHub_Model_Cron_Sales_Order extends BSeller_SkyHub_Model_Cron_Sales_Abstract
{

    /**
     * This method is not mapped (being used) anywhere because it can be harmful to store performance.
     * This is just a method created for tests and used when there's no order in the queue (SkyHub) to be consumed.
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function syncAllOrders(Mage_Cron_Model_Schedule $schedule)
    {
        if (!$this->canRun($schedule)) {
            return;
        }

        $orders = (array) $this->orderIntegrator()->orders();

        foreach ($orders as $orderData) {
            try {
                /** @var Mage_Sales_Model_Order $order */
                $order = $this->salesOrderProcessor()->createOrder($orderData);
            } catch (Exception $e) {
                Mage::logException($e);
                continue;
            }

            if (!$order || !$order->getId()) {
                continue;
            }

            $statusType  = $this->arrayExtract($orderData, 'status/type');
            $statusCode  = $this->arrayExtract($orderData, 'status/code');
            // $statusLabel = $this->arrayExtract($orderData, 'status/label');

            $this->salesOrderStatusProcessor()->processOrderStatus($statusCode, $statusType, $order);

            $message  = $schedule->getMessages();

            if ($order->getData('is_created')) {
                $message .= $this->__('Order ID %s was successfully created.', $order->getIncrementId());
            } elseif ($order->hasDataChanges()) {
                $message .= $this->__('Order ID %s was updated.', $order->getIncrementId());
            }

            $schedule->setMessages($message);
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
