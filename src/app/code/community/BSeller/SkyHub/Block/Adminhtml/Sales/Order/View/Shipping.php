<?php

class BSeller_SkyHub_Block_Adminhtml_Sales_Order_View_Shipping extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShow()
    {
        $order = Mage::registry('current_order');
        return $order->getData('bseller_skyhub');
    }

    /**
     * Get estimate delivery date from skyhub
     *
     * @return string
     */
    public function getDeliveryDate(){

        if(!$this->canShow()){
            return '';
        }

        $integrationData = $this->getIntegrationData();

        if(empty($integrationData['estimated_delivery'])){
            return '';
        }

        return $integrationData['estimated_delivery'];
    }

    /**
     * Get Integration Json
     *
     * @return string
     */
    public function getIntegrationData()
    {
        $order = Mage::registry('current_order');
        $data = json_decode($order->getData('bseller_skyhub_json'), true);
        return $data;
    }
}