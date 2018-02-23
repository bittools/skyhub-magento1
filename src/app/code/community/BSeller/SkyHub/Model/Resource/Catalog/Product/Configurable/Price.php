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
class BSeller_SkyHub_Model_Resource_Catalog_Product_Configurable_Price
    extends Mage_Catalog_Model_Resource_Product_Type_Configurable_Attribute
{
    
    /**
     * @param int $productId
     * @param array $attributeFilters
     *
     * @return array
     */
    public function getConfigurableOptionPrices($productId, $attributeFilters)
    {
        $attributesIds = array_keys($attributeFilters);
        
        $select = $this->getReadConnection()
            ->select()
            ->from(['cpsa' => $this->getMainTable()], 'attribute_id')
            ->joinInner(
                ['cpsap' => $this->getTable('catalog/product_super_attribute_pricing')],
                'cpsa.product_super_attribute_id = cpsap.product_super_attribute_id',
                ['value_index', 'is_percent', 'pricing_value']
            )
            ->where('cpsa.product_id = ?', (int) $productId)
            ->where('cpsa.attribute_id IN (?)', $attributesIds)
            ->where('cpsap.value_index IN (?)', $attributeFilters);
        
        $result = $this->getReadConnection()->fetchAll($select);
        
        return (array) $result;
    }

}