<?php

class BSeller_SkyHub_Model_System_Config_Source_Entity_Types extends BSeller_Core_Model_System_Config_Source_Abstract
{

    /**
     * @return array
     */
    protected function optionsKeyValue()
    {
        return [
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY          => $this->__('Catalog Category'),
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT           => $this->__('Catalog Product'),
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE => $this->__('Catalog Product Attribute'),
            BSeller_SkyHub_Model_Entity::TYPE_SALES_ORDER               => $this->__('Sales Order'),
        ];
    }

}
