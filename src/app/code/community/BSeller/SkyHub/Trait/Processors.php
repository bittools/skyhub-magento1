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
 * Access https://ajuda.skyhub.com.br/hc/pt-br/requests/new for questions and other requests.
 */

trait BSeller_SkyHub_Trait_Processors
{
    
    /**
     * @return BSeller_SkyHub_Model_Processor_Sales_Order_Status
     */
    protected function salesOrderStatusProcessor()
    {
        /** @var BSeller_SkyHub_Model_Processor_Sales_Order_Status $processor */
        $processor = Mage::getModel('bseller_skyhub/processor_sales_order_status');
        return $processor;
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Processor_Sales_Order
     */
    protected function salesOrderProcessor()
    {
        /** @var BSeller_SkyHub_Model_Processor_Sales_Order $processor */
        $processor = Mage::getModel('bseller_skyhub/processor_sales_order');
        return $processor;
    }
}
