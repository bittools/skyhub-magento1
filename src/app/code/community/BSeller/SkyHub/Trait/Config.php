<?php

trait BSeller_SkyHub_Trait_Config
{

    use BSeller_Core_Trait_Config,
        BSeller_SkyHub_Trait_Config_Service,
        BSeller_SkyHub_Trait_Config_Log,
        BSeller_SkyHub_Trait_Config_Cron;


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
        return $this->getSkyHubModuleConfig($field, 'general');
    }


    /**
     * @return boolean
     */
    protected function isModuleEnabled()
    {
        return (bool) $this->getGeneralConfig('enabled');
    }
    
    
    /**
     * @return BSeller_SkyHub_Model_Config
     */
    protected function getSkyHubConfig()
    {
        return Mage::getSingleton('bseller_skyhub/config');
    }
}
