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

class BSeller_SkyHub_Model_System_Config_Source_Entity_Types extends BSeller_Core_Model_System_Config_Source_Abstract
{

    /**
     * @return array
     */
    protected function optionsKeyValue($multiselect = null)
    {
        return array(
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_CATEGORY          => $this->__('Catalog Category'),
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT           => $this->__('Catalog Product'),
            BSeller_SkyHub_Model_Entity::TYPE_CATALOG_PRODUCT_ATTRIBUTE => $this->__('Catalog Product Attribute'),
            BSeller_SkyHub_Model_Entity::TYPE_SALES_ORDER               => $this->__('Sales Order'),
        );
    }

}
