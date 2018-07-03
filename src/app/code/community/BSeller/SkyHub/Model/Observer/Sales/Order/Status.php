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
class BSeller_SkyHub_Model_Observer_Sales_Order_Status extends BSeller_SkyHub_Model_Observer_Sales_Abstract
{
    
    /**
     * @param Varien_Event_Observer $observer
     */
    public function processCompleteStatusOrder(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getData('order');
        
        if (!$this->validateOrder($order)) {
            return;
        }
        
        $this->processDeliveredCustomerStatus($order);
    }
    
    
    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return $this
     */
    protected function processDeliveredCustomerStatus(Mage_Sales_Model_Order $order)
    {
        $configStatus = $this->getDeliveredOrdersStatus();
        
        if (!$this->statusMatches($configStatus, $order->getStatus())) {
            return $this;
        }

        try {
            $this->getStoreIterator()
                 ->call($this->orderIntegrator(), 'delivery', array($order->getId()), $order->getStore());
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }
    
    
    /**
     * @param Mage_Sales_Model_Order $order
     *
     * @return bool
     */
    protected function validateOrder(Mage_Sales_Model_Order $order)
    {
        if (!$order || !$order->getId()) {
            return false;
        }
    
        if ($order->getState() != Mage_Sales_Model_Order::STATE_COMPLETE) {
            return false;
        }
        
        return true;
    }
}
