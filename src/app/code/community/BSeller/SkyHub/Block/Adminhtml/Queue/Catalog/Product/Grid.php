<?php

class BSeller_SkyHub_Block_Adminhtml_Queue_Catalog_Product_Grid extends BSeller_SkyHub_Block_Adminhtml_Widget_Grid
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
        $name->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'name');

        $condition  = "eav.entity_id = main_table.entity_id ";
        $condition .= "AND eav.attribute_id = '{$name->getId()}'";
        $condition .= "AND eav.store_id = '{$this->getStoreId()}'";

        /** @var BSeller_SkyHub_Model_Resource_Queue_Collection $collection */
        $collection->getSelect()
            ->joinLeft(
                ['entity' => 'catalog_product_entity'],
                "entity.entity_id = main_table.entity_id",
                ['sku']
            )->joinLeft(
                ['eav' => $name->getBackendTable()],
                $condition,
                ['product_name' => 'value']
            )
        ;

        $collection->addFieldToFilter('entity_type', BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT);

        return $collection;
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumnAfter('sku', [
            'header'       => $this->__('Product SKU'),
            'align'        => 'left',
            'type'         => 'text',
            'width'        => '200px',
            'index'        => 'sku',
            'filter_index' => 'entity.sku',
        ], 'entity_id');

        $this->addColumnAfter('product_name', [
            'header'       => $this->__('Product Name'),
            'align'        => 'left',
            'type'         => 'text',
            'index'        => 'product_name',
            'filter_index' => 'eav.value',
        ], 'sku');

        $this->sortColumnsByOrder();

        return $this;
    }
    
}
