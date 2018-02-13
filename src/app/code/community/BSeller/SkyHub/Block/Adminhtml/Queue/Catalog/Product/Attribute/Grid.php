<?php

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Product_Attribute_Grid
    extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid
{

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
                ['eav' => 'eav_attribute'],
                "eav.attribute_id = main_table.entity_id",
                ['attribute_code', 'frontend_label']
            )
        ;

        $collection->addFieldToFilter('entity_type', BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE);

        return $collection;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('attribute_code', [
            'header'       => $this->__('Attribute Code'),
            'align'        => 'left',
            'type'         => 'text',
            'width'        => '200px',
            'index'        => 'attribute_code',
            'filter_index' => 'eav.attribute_code',
        ], 'entity_id');

        $this->addColumnAfter('frontend_label', [
            'header'       => $this->__('Frontend Label'),
            'align'        => 'left',
            'type'         => 'text',
            'index'        => 'frontend_label',
            'filter_index' => 'eav.frontend_label',
        ], 'attribute_code');

        $this->sortColumnsByOrder();

        return $this;
    }
    
}
