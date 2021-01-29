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

class BSeller_SkyHub_Model_Resource_Setup extends BSeller_Core_Model_Resource_Setup
{
    
    use BSeller_SkyHub_Trait_Data,
        BSeller_SkyHub_Trait_Config;
    
    /**
     * @return $this
     */
    public function installSkyHubRequiredAttributes($entityType)
    {
        if ($entityType == Mage_Catalog_Model_Product::ENTITY) {
            $setup = Mage::getResourceModel('bseller_skyhub/setup_catalog_product_mapping', 'core_setup');
            $setup->installProductSkyHubRequiredAttributes();
        } else {
            $setup = Mage::getResourceModel('bseller_skyhub/setup_customer_mapping', 'core_setup');
            $setup->installCustomerSkyHubRequiredAttributes();
        }
    }

    /**
     * @param array $statuses
     *
     * @return $this
     */
    public function createAssociatedSalesOrderStatuses(array $states = array())
    {
        foreach ($states as $stateCode => $statuses) {
            $this->createSalesOrderStatus($stateCode, $statuses);
        }
        
        return $this;
    }
    
    
    /**
     * @param string $state
     * @param array  $status
     *
     * @return $this
     */
    public function createSalesOrderStatus($state, array $status)
    {
        foreach ($status as $statusCode => $statusLabel) {
            $statusData = array(
                'status' => $statusCode,
                'label'  => $statusLabel
            );
        
            $this->getConnection()->insertIgnore($this->getSalesOrderStatusTable(), $statusData);
            $this->associateStatusToState($state, $statusCode);
        }
        
        return $this;
    }
    
    
    /**
     * @param string $state
     * @param string $status
     * @param int    $isDefault
     *
     * @return $this
     */
    public function associateStatusToState($state, $status, $isDefault = 0)
    {
        $associationData = array(
            'status'     => (string) $status,
            'state'      => (string) $state,
            'is_default' => (int)    $isDefault,
        );
    
        $this->getConnection()->insertIgnore($this->getSalesOrderStatusStateTable(), $associationData);
        
        return $this;
    }
    
    
    /**
     * @return string
     */
    public function getSalesOrderStatusTable()
    {
        return $this->getTable('sales/order_status');
    }
    
    
    /**
     * @return string
     */
    public function getSalesOrderStatusStateTable()
    {
        return $this->getTable('sales/order_status_state');
    }
}
