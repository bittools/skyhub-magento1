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
 * @author    Bruno Gemelli <bruno.gemelli@e-smart.com.br>
 */
class BSeller_SkyHub_Block_Adminhtml_Sales_Order_View_Tab_Plp
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_plp');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }


    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'bseller_skyhub/shipment_plp_collection';
    }


    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    /**
     * @return BSeller_SkyHub_Model_Resource_Shipment_Plp_Collection
     */
    protected function _getCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addFieldToSelect('id', 'plp_id')
            ->addFieldToSelect('skyhub_code');

        $orderPlpTable = $collection->getResource()->getTable('bseller_skyhub/plp_orders');

        $collection->getSelect()
            ->join(
                array('plp_order' => $orderPlpTable),
                "plp_order.plp_id = main_table.id",
                array()
            );

        $collection->addFieldToFilter('skyhub_order_code', $this->getOrder()->getBsellerSkyhubCode());
        $collection->getSelect()->group('plp_id');

        return $collection;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'skyhub_code',
            array(
                'header'    => $this->getSkyhubHelper()->__('PLP Code'),
                'index'     => 'skyhub_code',
                'sortable'  => false,
            )
        );

        return parent::_prepareColumns();
    }


    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }


    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/bseller_skyhub_shipment_plp/view',
            array(
                'id'  => $row->getPlpId()
            )
        );
    }


    /**
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getSkyhubHelper()->__('SkyHub Order PLP');
    }


    /**
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getSkyhubHelper()->__('SkyHub Order PLP');
    }


    /**
     * @return bool
     */
    public function canShowTab()
    {
        $order = Mage::registry('current_order');
        if (!$order->getData('bseller_skyhub')) {
            return false;
        }

        $plpCollection = $this->_getCollection();
        if ((bool)$plpCollection->count() == false) {
            return false;
        }

        return true;
    }


    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }


    /**
     * @return BSeller_SkyHub_Helper_Data
     */
    protected function getSkyhubHelper()
    {
        return Mage::helper('bseller_skyhub');
    }
}
