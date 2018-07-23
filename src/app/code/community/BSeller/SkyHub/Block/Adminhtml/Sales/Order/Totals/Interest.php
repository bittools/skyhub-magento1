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

class BSeller_SkyHub_Block_Adminhtml_Sales_Order_Totals_Interest extends BSeller_Core_Block_Abstract
{
    
    /**
     * @return $this
     */
    public function initTotals()
    {
        /** @var Mage_Sales_Block_Order_Totals $block */
        $block = $this->getParentBlock();
        
        if (!$block) {
            return $this;
        }
        
        /** @var Mage_Sales_Model_Order $order */
        $order = $block->getOrder();
        
        if (!$order) {
            return $this;
        }
        
        $block->addTotal(new Varien_Object(array(
            'code'       => 'bseller_skyhub_interest',
            'strong'     => true,
            'value'      => $order->getData('bseller_skyhub_interest'),
            'base_value' => $order->getData('bseller_skyhub_interest'),
            'label'      => $this->__('BSeller Interest'),
            'area'       => 'footer'
        )));
        
        return $this;
    }
}
