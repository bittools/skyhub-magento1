<?php

trait BSeller_SkyHub_Trait_Config
{

    use BSeller_Core_Trait_Config;


    /**
     * @param string                      $field
     * @param string                      $group
     * @param Mage_Core_Model_Config|null $store
     *
     * @return mixed
     */
    protected function getSkyHubModuleConfig($field, $group, Mage_Core_Model_Config $store = null)
    {
        return $this->getModuleConfig($field, $group, 'bseller_skyhub', $store);
    }


    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getGeneralConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'service');
    }


    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getLogConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'log');
    }


    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getServiceConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'service');
    }


    /**
     * @param string $field
     *
     * @return string|integer
     */
    protected function getCatalogProductIntegrationConfig($field)
    {
        return $this->getSkyHubModuleConfig($field, 'catalog_product_integration');
    }


    /**
     * @return boolean
     */
    protected function isModuleEnabled()
    {
        return (bool) $this->getGeneralConfig('enabled');
    }


    /**
     * @return boolean
     */
    protected function isLogEnabled()
    {
        return (bool) $this->getLogConfig('enabled');
    }
    
    
    /**
     * @return int
     */
    protected function getCatalogProductIntegrationMethod()
    {
        return (int) $this->getCatalogProductIntegrationConfig('method');
    }


    /**
     * @return string
     */
    protected function getLogFilename()
    {
        return (string) $this->getLogConfig('filename');
    }


    /**
     * @return string
     */
    protected function getServiceBaseUri()
    {
        return (string) $this->getServiceConfig('base_uri');
    }


    /**
     * @return string
     */
    protected function getServiceEmail()
    {
        return (string) $this->getServiceConfig('email');
    }


    /**
     * @return string
     */
    protected function getServiceApiKey()
    {
        return (string) $this->getServiceConfig('api_key');
    }


    /**
     * @return string
     */
    protected function getServiceApiToken()
    {
        return (string) $this->getServiceConfig('api_token');
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Config
     */
    protected function getSkyHubConfig()
    {
        return Mage::getSingleton('bseller_skyhub/config');
    }
}
