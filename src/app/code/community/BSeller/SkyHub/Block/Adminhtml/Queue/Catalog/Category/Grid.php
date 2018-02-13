<?php

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Category_Grid extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid
{

    /**
     * @param BSeller_SkyHub_Model_Resource_Queue_Collection $collection
     *
     * @return BSeller_SkyHub_Model_Resource_Queue_Collection
     *
     * @throws Mage_Core_Exception
     */
    protected function getPreparedCollection(BSeller_SkyHub_Model_Resource_Queue_Collection $collection)
    {
        /** @var Mage_Eav_Model_Entity_Attribute $name */
        $name = Mage::getModel('eav/entity_attribute');
        $name->loadByCode(Mage_Catalog_Model_Category::ENTITY, 'name');

        $condition  = "eav.entity_id = main_table.entity_id ";
        $condition .= "AND eav.entity_type_id = '{$name->getEntityTypeId()}' ";
        $condition .= "AND eav.attribute_id = '{$name->getId()}' ";
        $condition .= "AND eav.store_id = '{$this->getStoreId()}' ";

        /** @var BSeller_SkyHub_Model_Resource_Queue_Collection $collection */
        $collection->getSelect()
            ->joinLeft(['eav' => $name->getBackendTable()], $condition, ['category_name' => 'value'])
        ;

        $collection->addFieldToFilter('entity_type', BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY);

        return $collection;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('category_name', [
            'header'       => $this->__('Category Name'),
            'align'        => 'left',
            'type'         => 'text',
            'filter_index' => 'eav.value',
        ], 'entity_id');

        $this->sortColumnsByOrder();

        return $this;
    }
    
}
