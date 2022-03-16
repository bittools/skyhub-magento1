<?php

class BSeller_SkyHub_Block_Adminhtml_Sales_Order_View_Tab_B2wDirectInvoiced
    extends Mage_Adminhtml_Block_Sales_Order_Abstract
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var string
     */
    protected $bsellerSkyhubJson;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('bseller/skyhub/order/view/tab/b2w_direct_invoiced.phtml');
    }

    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->_helper()->__('SkyHub Order Xml Nfe');
    }

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->_helper()->__('SkyHub Order Xml Nfe');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $calculationType = '';
        if (isset($this->getIntegrationJson()['calculation_type'])) {
            $calculationType = $this->getIntegrationJson()['calculation_type'];
        }

        $approved = BSeller_SkyHub_Model_System_Config_Source_Skyhub_Status_Types::TYPE_APPROVED;
        return $calculationType == 'b2wentregadirect'
            && $this->getOrder()->getBsellerSkyhubStatus() == $approved;
    }

    /**
     * Return order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Get Integration Json
     *
     * @return array
     */
    public function getIntegrationJson()
    {
        if ($this->bsellerSkyhubJson) {
            return $this->bsellerSkyhubJson;
        }

        $jsonFormat = json_decode(
            $this->getOrder()->getData('bseller_skyhub_json'),
            true
        );
        $this->bsellerSkyhubJson = $jsonFormat;
        return $this->bsellerSkyhubJson;
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
}
