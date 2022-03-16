<?php

class BSeller_SkyHub_Block_Adminhtml_Sales_Order_View_Tab_IntegrationJson
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Internal constructor, that is called from real constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('bseller/skyhub/order/view/tab/integration_json.phtml');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('SkyHub Order Data (Json)');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('SkyHub Order Data (Json)');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $order = Mage::registry('current_order');
        return $order->getData('bseller_skyhub');
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    protected function _helper()
    {
        return Mage::helper('bseller_skyhub');
    }

    /**
     * Get Integration Json
     *
     * @return string
     */
    public function getIntegrationJson()
    {
        $order = Mage::registry('current_order');
        $jsonFormat = json_encode(json_decode($order->getData('bseller_skyhub_json'), true), JSON_PRETTY_PRINT);
        return $jsonFormat;
    }
}