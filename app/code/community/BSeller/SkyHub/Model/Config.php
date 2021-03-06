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

abstract class BSeller_SkyHub_Model_Config
{
    
    /** @var Mage_Core_Model_Config_Base */
    protected $config;

    /** @var array */
    protected $attributes = array();

    /** @var array */
    protected $blacklist = array();


    /**
     * @param string $code
     *
     * @return array
     */
    public function getAttributeInstallConfig($code)
    {
        $this->getSkyHubFixedAttributes();

        if (!isset($this->attributes[$code], $this->attributes[$code]['attribute_install_config'])) {
            return array();
        }

        return (array)$this->attributes[$code]['attribute_install_config'];
    }
    
    /**
     * @param string $attributeCode
     *
     * @return bool
     */
    public function isAttributeCodeInBlacklist($attributeCode)
    {
        $blacklist = $this->getBlacklistedAttributes();
        return in_array($attributeCode, $blacklist);
    }
    
    
    /**
     * @return Mage_Core_Model_Config_Base
     */
    public function getConfig()
    {
        if (empty($this->config)) {
            $this->config = Mage::app()->getConfig()->loadModulesConfiguration('skyhub.xml');
        }
    
        return $this->config;
    }

}
