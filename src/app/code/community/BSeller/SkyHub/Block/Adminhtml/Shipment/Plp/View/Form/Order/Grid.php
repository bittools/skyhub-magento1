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
class BSeller_SkyHub_Block_Adminhtml_Shipment_Plp_View_Form_Order_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('plp_orders');
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
        return 'bseller_skyhub/shipment_plp_order_collection';
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
            ->addFieldToSelect('skyhub_order_code');

        $plpTable = $collection->getResource()->getTable('bseller_skyhub/plp');

        $collection->getSelect()
            ->join(
                array('plp' => $plpTable),
                "plp.id = main_table.plp_id",
                array()
            );

        $collection->addFieldToFilter('plp_id', $this->getPlp()->getId());

        return $collection;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'skyhub_order_code',
            array(
                'header'    => $this->getSkyhubHelper()->__('SkyHub Order code'),
                'index'     => 'skyhub_order_code',
                'sortable'  => false,
                'width'     => 300,
            )
        );

        return parent::_prepareColumns();
    }


    /**
     * @param $row
     * @return string
     */
    public function getRowUrl($row)
    {
        $order = Mage::getModel('sales/order');
        $order->load($row->getSkyhubOrderCode(), 'bseller_skyhub_code');
        if (!($order instanceof Varien_Object) || !$order->getId()) {
            return '';
        }

        return $this->getUrl(
            '*/bseller_skyhub_shipment_plp/view',
            array(
                'id'  => $order->getId()
            )
        );
    }


    /**
     * Retrieve PLP model instance
     *
     * @return BSeller_SkyHub_Model_Shipment_Plp
     */
    protected function getPlp()
    {
        return Mage::registry('current_plp');
    }


    /**
     * @return BSeller_SkyHub_Helper_Data
     */
    protected function getSkyhubHelper()
    {
        return Mage::helper('bseller_skyhub');
    }
}
