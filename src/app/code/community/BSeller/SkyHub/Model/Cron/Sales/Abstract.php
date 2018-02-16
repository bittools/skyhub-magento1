<?php

abstract class BSeller_SkyHub_Model_Cron_Sales_Abstract extends BSeller_SkyHub_Model_Cron_Abstract
{

    /**
     * @return BSeller_SkyHub_Model_Integrator_Sales_Order_Queue
     */
    protected function getOrderQueueIntegrator()
    {
        return Mage::getSingleton('bseller_skyhub/integrator_sales_order_queue');
    }

    /**
     * @return BSeller_SkyHub_Model_Integrator_Sales_Order
     */
    protected function getOrderIntegrator()
    {
        return Mage::getSingleton('bseller_skyhub/integrator_sales_order');
    }


    /**
     * @return BSeller_SkyHub_Model_Processor_Sales_Order
     */
    protected function getOrderProcessor()
    {
        return Mage::getModel('bseller_skyhub/processor_sales_order');
    }


    /**
     * @return BSeller_SkyHub_Model_Processor_Sales_Order_Status
     */
    protected function getOrderStatusProcessor()
    {
        return Mage::getModel('bseller_skyhub/processor_sales_order_status');
    }
}
