<?php

class BSeller_SkyHub_Model_Entity extends BSeller_Core_Model_Abstract
{

    const TYPE_CATALOG_PRODUCT_ATTRIBUTE = 'catalog_product_attribute';
    const TYPE_CATALOG_PRODUCT           = 'catalog_product';
    const TYPE_CATALOG_CATEGORY          = 'catalog_category';
    const TYPE_SALES_ORDER               = 'sales_order';


    protected function _construct()
    {
        $this->_init('bseller_skyhub/entity');
    }

}
