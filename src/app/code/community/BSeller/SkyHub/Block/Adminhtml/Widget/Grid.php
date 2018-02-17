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

abstract class BSeller_SkyHub_Block_Adminhtml_Widget_Grid extends BSeller_Core_Block_Adminhtml_Widget_Grid
{

    /**
     * @param BSeller_SkyHub_Model_Resource_Queue_Collection $collection
     *
     * @return BSeller_SkyHub_Model_Resource_Queue_Collection
     */
    protected abstract function getPreparedCollection(BSeller_SkyHub_Model_Resource_Queue_Collection $collection);


    protected function _prepareCollection()
    {
        $collection = $this->getPreparedCollection($this->getQueueCollection());
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'       => $this->__('Entity ID'),
            'align'        => 'center',
            'width'        => '50px',
            'type'         => 'text',
            'filter_index' => 'main_table.entity_id',
        ));

        /** @var BSeller_SkyHub_Model_System_Config_Source_Entity_Types $source */
//        $source =Mage::getModel('bseller_skyhub/system_config_source_entity_types');
//        $this->addColumn('entity_type', array(
//            'header'       => $this->__('Entity Type'),
//            'align'        => 'left',
//            'width'        => '200px',
//            'type'         => 'options',
//            'filter_index' => 'main_table.entity_type',
//            'options'      => $source->toArray(),
//        ));

        /** @var BSeller_SkyHub_Model_System_Config_Source_Queue_Status $source */
        $source = Mage::getModel('bseller_skyhub/system_config_source_queue_status');
        $this->addColumn('status', array(
            'header'       => $this->__('Status'),
            'align'        => 'left',
            'width'        => '75px',
            'type'         => 'options',
            'filter_index' => 'main_table.status',
            'options'      => $source->toArray(),
        ));

        $this->addColumn('messages', array(
            'header'       => $this->__('Messages'),
            'align'        => 'left',
            'type'         => 'text',
            'filter_index' => 'main_table.messages',
        ));

        $this->addColumn('can_process', array(
            'header'       => $this->__('Can Process'),
            'align'        => 'center',
            'width'        => '50px',
            'type'         => 'options',
            'filter_index' => 'main_table.can_process',
            'options'      => [
                0 => $this->__('No'),
                1 => $this->__('Yes'),
            ],
        ));

        $this->addColumn('process_after', array(
            'header'       => $this->__('Process After'),
            'align'        => 'left',
            'width'        => '50px',
            'type'         => 'datetime',
            'filter_index' => 'main_table.process_after',
        ));

        parent::_prepareColumns();
        return $this;
    }


    /**
     * @param $item
     *
     * @return bool|string
     */
    public function getRowUrl($item)
    {
        return false;
    }


    /**
     * @return BSeller_SkyHub_Model_Resource_Queue_Collection
     */
    protected function getQueueCollection()
    {
        /** @var BSeller_SkyHub_Model_Resource_Queue_Collection $collection */
        $collection = Mage::getResourceModel('bseller_skyhub/queue_collection');
        return $collection;
    }


    /**
     * @return int
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }


    /**
     * @return int
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    protected function getAdminStoreId()
    {
        return Mage::app()->getStore(Mage_Core_Model_Store::ADMIN_CODE)->getId();
    }

}
