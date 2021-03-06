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

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Product_Attribute_Grid
    extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid
{

    /**
     * @var string
     */
    protected $_entityType = BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE;

    /**
     * @param BSeller_SkyHub_Model_Resource_Queue_Collection $collection
     *
     * @return BSeller_SkyHub_Model_Resource_Queue_Collection
     */
    protected function getPreparedCollection(BSeller_SkyHub_Model_Resource_Queue_Collection $collection)
    {
        /** @var BSeller_SkyHub_Model_Resource_Queue_Collection $collection */
        $collection->getSelect()
            ->joinLeft(
                array('eav' => Mage::getSingleton('core/resource')->getTableName('eav/attribute')),
                "eav.attribute_id = main_table.entity_id",
                array('attribute_code', 'frontend_label')
            );

        $collection->addFieldToFilter('entity_type', BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE);

        return $collection;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter(
            'attribute_code',
            array(
                'header'       => $this->__('Attribute Code'),
                'align'        => 'left',
                'type'         => 'text',
                'width'        => '200px',
                'index'        => 'attribute_code',
                'filter_index' => 'eav.attribute_code',
            ),
            'entity_id'
        );

        $this->addColumnAfter(
            'frontend_label',
            array(
                'header'       => $this->__('Frontend Label'),
                'align'        => 'left',
                'type'         => 'text',
                'index'        => 'frontend_label',
                'filter_index' => 'eav.frontend_label',
            ),
            'attribute_code'
        );

        $this->sortColumnsByOrder();

        return $this;
    }
    
}
