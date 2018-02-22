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
class BSeller_SkyHub_Model_Resource_Eav_Entity_Attribute_Option extends Mage_Eav_Model_Resource_Entity_Attribute_Option
{
    
    /**
     * @param int|Mage_Eav_Model_Entity_Attribute $attribute
     * @param int                                 $optionId
     * @param int|Mage_Core_Model_Store           $store
     *
     * @return mixed|null
     *
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getAttributeOptionText($attribute, $optionId, $store = null)
    {
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute) {
            $attribute = $attribute->getId();
        }
        
        if (empty($store)) {
            $store = Mage::app()->getStore()->getId();
        }
        
        if ($store instanceof Mage_Core_Model_Store) {
            $store = (int) $store->getId();
        }
        
        /** @var Varien_Db_Select $select */
        $select = $this->getReadConnection()
             ->select()
            ->from(['o' => $this->getMainTable()])
            ->joinInner(
                ['ov' => $this->getTable('eav/attribute_option_value')],
                "o.option_id = ov.option_id",
                ['store_id', 'value']
            )
            ->where('o.attribute_id = ?', (int) $attribute)
            ->where('ov.store_id IN (?)', (array) $this->filterIds([0, $store]))
            ->where('o.option_id = ?', (int) $optionId)
            ->order('ov.store_id DESC')
        ;
        
        try {
            $results = $this->getReadConnection()->fetchAll($select);
    
            if (empty($results)) {
                return null;
            }
    
            foreach ((array) $results as $result) {
                $resultStoreId = (int) $result['store_id'];
                if ($resultStoreId === $store) {
                    return $this->returnValue($result);
                }
            }
    
            return $this->returnValue((array) array_pop($results));
        } catch (Exception $e) {
            Mage::logException($e);
        }
        
        return null;
    }
    
    
    /**
     * @param array $data
     *
     * @return mixed
     */
    protected function returnValue(array $data)
    {
        return $data['value'];
    }
    
    
    /**
     * @param array $ids
     *
     * @return array
     */
    protected function filterIds(array $ids)
    {
        $ids = array_unique($ids);
        return $ids;
    }

}