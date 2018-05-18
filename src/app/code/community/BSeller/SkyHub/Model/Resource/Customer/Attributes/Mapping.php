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
 * @author    Julio Reis <julio.reis@e-smart.com.br>
 */

class BSeller_SkyHub_Model_Resource_Customer_Attributes_Mapping extends BSeller_Core_Model_Resource_Abstract
{
    
    public function _construct()
    {
        $this->_init('bseller_skyhub/customer_attributes_mapping', 'id');
    }

    /**
     * @param $id
     * @return array|bool|null
     */
    public function getOptionsByAttributeMappingId($id)
    {
        if (!$id)
            return null;

        $select = $this->getReadConnection()
            ->select()
            ->from('bseller_skyhub_customer_attributes_mapping_options', '*')
            ->where('customer_attributes_mapping_id = ?', (int)$id);

        try {
            return $this->getReadConnection()->fetchAll($select);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * @param $id
     * @param $skyhubCode
     * @return bool|null|string
     */
    public function getMagentoValueOption($id, $skyhubCode)
    {
        if (!$id || !$skyhubCode)
            return null;

        $select = $this->getReadConnection()
            ->select()
            ->from('bseller_skyhub_customer_attributes_mapping_options', 'magento_value')
            ->where('customer_attributes_mapping_id = ?', (int)$id)
            ->where('skyhub_code = ?', $skyhubCode);

        try {
            return $this->getReadConnection()->fetchOne($select);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
    }

    /**
     * @param $attributeMappingId
     * @param $skyhubCode
     * @param $magentoValue
     */
    public function updateOption($attributeMappingId, $skyhubCode, $magentoValue)
    {
        $table = $this->getTable('bseller_skyhub/customer_attributes_mapping_options');
        $this->_getWriteAdapter()->update(
            $table,
            ['magento_value' => $magentoValue],
            "customer_attributes_mapping_id = '{$attributeMappingId}' and skyhub_code = '{$skyhubCode}'"
        );
    }

}