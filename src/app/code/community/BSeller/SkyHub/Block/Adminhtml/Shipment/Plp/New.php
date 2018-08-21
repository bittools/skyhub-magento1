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

class BSeller_SkyHub_Block_Adminhtml_Shipment_Plp_New
    extends BSeller_Core_Block_Adminhtml_Widget_Grid
{

    use BSeller_SkyHub_Trait_Service;
    use BSeller_SkyHub_Trait_Integrators;
    use BSeller_SkyHub_Trait_Sales_Order;


    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /**
         * @var array                                       $skyhubOrdersToGroup
         * @var Mage_Sales_Model_Resource_Order_Collection  $magentoOrders
         * @var array                                       $mutualIds
         */
        $skyhubOrdersToGroup = $this->_getSkyHubOrdersToGroup();
        $magentoOrders       = $this->_getMagentoOrders();
        $mutualIds           = $this->_getMutualOrdersToGroup($skyhubOrdersToGroup, $magentoOrders);

        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getResourceModel('sales/order_grid_collection')
            ->addFieldtoFilter('entity_id', ['in' => $mutualIds]);

        $collection->getSelect()
            ->join(
                array('order' => $magentoOrders->getResource()->getTable('sales/order')),
                'order.entity_id = main_table.entity_id',
                array('order.bseller_skyhub_code')
            );

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn(
            'real_order_id',
            array(
                'header'=> Mage::helper('sales')->__('Order #'),
                'width' => '80px',
                'type'  => 'text',
                'index' => 'increment_id',
            )
        );

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                array(
                    'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                    'index'     => 'store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
                    'escape'  => true,
                )
            );
        }

        $this->addColumn(
            'created_at',
            array(
                'header' => Mage::helper('sales')->__('Purchased On'),
                'index' => 'created_at',
                'type' => 'datetime',
                'width' => '100px',
            )
        )
        ->addColumn(
            'billing_name',
            array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index' => 'billing_name',
            )
        )
        ->addColumn(
            'shipping_name',
            array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index' => 'shipping_name',
            )
        )
        ->addColumn(
            'grand_total',
            array(
                'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
                'index' => 'grand_total',
                'type'  => 'currency',
                'currency' => 'order_currency_code',
            )
        )
        ->addColumn(
            'status',
            array(
                'header' => Mage::helper('sales')->__('Status'),
                'index' => 'status',
                'type'  => 'options',
                'width' => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            )
        );

        return $this;
    }


    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->setMassactionIdField('bseller_skyhub_code');

        /** @var Mage_Adminhtml_Block_Widget_Grid_Massaction $massactionBlock */
        $massactionBlock = $this->getMassactionBlock();
        $massactionBlock->setFormFieldName('skyhub_order_ids');
        $massactionBlock->addItem(
            'group',
            array(
                'label'     => $this->__('Group'),
                'url'       => $this->getUrl('*/*/massGroup'),
                'confirm'   => $this->__('Are you sure?'),
            )
        );

        return $this;
    }


    /**
     * Get all Magento orders from SkyHub orders received
     *
     * @param array $skyHubOrdersToGroup
     * @param Mage_Sales_Model_Resource_Order_Collection $magentoOrders
     *
     * @return array
     */
    protected function _getMutualOrdersToGroup($skyHubOrdersToGroup, $magentoOrders)
    {
        $mutualIds = array();

        foreach ($magentoOrders as $order) {
            if (in_array($order->getBsellerSkyhubCode(), $skyHubOrdersToGroup)) {
                $mutualIds[] = $order->getId();
            }
        }

        return $mutualIds;
    }


    /**
     * Get all orders available to group in SkyHub API
     *
     * @return \SkyHub\Api\Handler\Response\HandlerInterface
     */
    protected function _getSkyHubOrdersToGroup()
    {
        $skyHubOrdersCode = array();

        /** @var array $ordersToGroupData */
        $ordersToGroupData =  $this->shipmentPlpIntegrator()->getOrdersAvailableToGroup();

        foreach ($ordersToGroupData as $order) {
            $skyHubOrdersCode[] = $order['code'];
        }

        return $skyHubOrdersCode;
    }


    /**
     * Get Magento orders imported from SkyHub
     *
     * @todo filter by only skyhub orders? if yes.. fallback for bizzcommerce column?
     *
     * @return array
     */
    protected function _getMagentoOrders()
    {
        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = $this->getPendingOrdersFromSkyHub();

        $collection->getSelect()->reset('columns');

        $collection->addFieldToSelect('entity_id')
            ->addFieldToSelect('bseller_skyhub_code');

        return $collection;
    }
}
